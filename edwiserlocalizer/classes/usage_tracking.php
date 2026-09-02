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
 *  @package    local_edwiserlocalizer
 *  @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  @author     Harshal Thakare
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
     * defers the first send until 2 days after install, and only re-sends
     * when the 'translateto' language list is modified.
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

        $firstinstall = get_config('local_edwiserlocalizer', 'usage_data_first_install_time');
        $lastsent = get_config('local_edwiserlocalizer', 'usage_data_last_sent');

        // Fresh install: record the first encounter and wait 2 days before sending.
        if (!$firstinstall) {
            set_config('usage_data_first_install_time', time(), 'local_edwiserlocalizer');
            return;
        }

        // Enforce the 2-day deferral from first install / first tracking encounter.
        $twodayseconds = 2 * 24 * 60 * 60;
        if (time() - $firstinstall < $twodayseconds) {
            return;
        }

        $lasttranslateto = get_config('local_edwiserlocalizer', 'usage_data_last_translateto');
        $currenttranslateto = get_config('local_edwiserlocalizer', 'translateto');

        // After the initial send, only re-trigger when the language list changes.
        if ($lastsent && $lasttranslateto === $currenttranslateto) {
            return;
        }

        $analyticsdata = $this->prepare_usage_analytics();

        $sender = new feedback_sender();
        $resultarr = $sender->send_feedback($analyticsdata);

        // Mark as sent and snapshot the current translateto value so we know
        // whether it changes for the next potential re-send.
        if (!isset($resultarr['status']) || $resultarr['status'] !== false) {
            set_config('usage_data_last_sent', time(), 'local_edwiserlocalizer');
            set_config('usage_data_last_translateto', $currenttranslateto, 'local_edwiserlocalizer');
        }
    }

    /**
     * Prepare the usage analytics payload.
     *
     * @return array
     */
    private function prepare_usage_analytics(): array {
        global $CFG, $DB, $SITE;

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
        $placement = !empty($pluginconfig->placement) ? (int) $pluginconfig->placement : 0;
        $showicon  = !empty($pluginconfig->showicon) ? (int) $pluginconfig->showicon : 0;
        $appearance = !empty($pluginconfig->appearance) ? (int) $pluginconfig->appearance : 0;

        $placementmap = [
            0 => get_string('header', 'local_edwiserlocalizer'),
            1 => get_string('footer', 'local_edwiserlocalizer'),
        ];
        $showiconmap = [
            0 => get_string('across_site', 'local_edwiserlocalizer'),
            1 => get_string('coursepage', 'local_edwiserlocalizer'),
        ];
        $appearancemap = [
            0 => get_string('light', 'local_edwiserlocalizer'),
            1 => get_string('dark', 'local_edwiserlocalizer'),
        ];

        return [
            'siteurl'          => $this->detect_site_type() . preg_replace('#^https?://#', '', rtrim($CFG->wwwroot, '/')),
            'site_name'        => !empty($SITE->fullname) ? $SITE->fullname : '',
            'admin_email'      => $adminemail,
            'email'            => !empty($CFG->supportemail) ? $CFG->supportemail : '',
            'product_name'     => 'Edwiser Localizer',
            'moodle_version'   => $this->get_moodle_major_version(),
            'product_settings' => [
                'enable'         => !empty($pluginconfig->enable) ? (int) $pluginconfig->enable : 0,
                'placement'      => $placementmap[$placement] ?? 'Unknown',
                'showicon'       => $showiconmap[$showicon] ?? 'Unknown',
                'translateto'    => !empty($pluginconfig->translateto) ? $pluginconfig->translateto : '',
                'appearance'     => $appearancemap[$appearance] ?? 'Unknown',
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
