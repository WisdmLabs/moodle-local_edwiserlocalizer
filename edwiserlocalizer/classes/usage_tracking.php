<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edwiser Localizer Usage Tracking.
 *
 * @package    local_edwiserlocalizer
 * @copyright  2026 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserlocalizer;

/**
 * Usage tracking for Edwiser Localizer plugin.
 */
class usage_tracking {

    /**
     * Send usage analytics to Edwiser.
     *
     * Only runs for site admins, respects the enableusagetracking setting,
     * and throttles to once every 7 days.
     */
    public function send_usage_analytics() {
        global $CFG, $USER;

        // Only execute for site admins to reduce unnecessary API calls.
        if (!is_siteadmin()) {
            return;
        }

        // Check user consent.
        $consent = get_config('local_edwiserlocalizer', 'enableusagetracking');
        if (!$consent) {
            return;
        }

        $lastsent = isset($CFG->usage_data_last_sent_local_edwiserlocalizer)
            ? $CFG->usage_data_last_sent_local_edwiserlocalizer
            : false;

        // Only send if 7 days have passed since last send.
        if ($lastsent && time() <= $lastsent) {
            return;
        }

        $analyticsdata = json_encode($this->prepare_usage_analytics());

        $url = 'https://edwiser.org/wp-json/edwiser_customizations/send_usage_data';

        $ch = curl_init();
        $useragent = $_SERVER['HTTP_USER_AGENT'] . ' - ' . $CFG->wwwroot;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $analyticsdata);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($analyticsdata),
        ]);

        $result = curl_exec($ch);
        $resultarr = [];
        if ($result) {
            $resultarr = json_decode($result, true);
        }
        curl_close($ch);

        // Save new timestamp only if API returned success.
        if (!empty($resultarr['success'])) {
            set_config('usage_data_last_sent_local_edwiserlocalizer', time() + 604800);
        }
    }

    /**
     * Prepare the usage analytics payload.
     *
     * @return array
     */
    private function prepare_usage_analytics(): array {
        global $CFG, $DB;

        $pluginconfig = get_config('local_edwiserlocalizer');

        // Get primary admin email.
        $adminemail = '';
        if (!empty($CFG->siteadmins)) {
            $adminids = explode(',', $CFG->siteadmins);
            $firstadmin = $DB->get_record('user', ['id' => (int) $adminids[0], 'deleted' => 0], 'email');
            if ($firstadmin) {
                $adminemail = $firstadmin->email;
            }
        }
        if (empty($adminemail) && !empty($CFG->supportemail)) {
            $adminemail = $CFG->supportemail;
        }

        return [
            'siteurl'          => $this->detect_site_type() . preg_replace('#^https?://#', '', rtrim($CFG->wwwroot, '/')),
            'site_name'        => !empty($CFG->fullname) ? $CFG->fullname : '',
            'admin_email'      => $adminemail,
            'email'            => !empty($CFG->supportemail) ? $CFG->supportemail : '',
            'product_name'     => 'Edwiser Localizer',
            'moodle_version'   => $this->get_moodle_major_version(),
            'product_settings' => [
                'enable'         => !empty($pluginconfig->enable) ? (int) $pluginconfig->enable : 0,
                'placement'      => !empty($pluginconfig->placement) ? (int) $pluginconfig->placement : 0,
                'showicon'       => !empty($pluginconfig->showicon) ? (int) $pluginconfig->showicon : 0,
                'translateto'    => !empty($pluginconfig->translateto) ? $pluginconfig->translateto : '',
                'appearance'     => !empty($pluginconfig->appearance) ? (int) $pluginconfig->appearance : 0,
                'version'        => !empty($pluginconfig->version) ? $pluginconfig->version : '',
            ],
        ];
    }

    /**
     * Extract the major.minor Moodle version (e.g. 4.5, 5.0) from $CFG->release.
     *
     * @return string
     */
    private function get_moodle_major_version(): string {
        global $CFG;

        if (empty($CFG->release)) {
            return 'unknown';
        }

        // Release strings look like "4.5.2 (Build: 20250210)" or "5.0".
        if (preg_match('/^(\d+\.\d+)/', $CFG->release, $matches)) {
            return $matches[1];
        }

        return 'unknown';
    }

    /**
     * Detect if the site is running on localhost.
     *
     * @return string 'localsite--' if localhost, otherwise empty string.
     */
    private function detect_site_type(): string {
        $whitelist = ['127.0.0.1', '::1'];

        if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $whitelist, true)) {
            return 'localsite--';
        }

        return '';
    }
}
