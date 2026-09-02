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
 * Feedback sender for Edwiser Localizer usage tracking.
 *
 * Sends usage data to the Edwiser feedback endpoint in Q&A format.
 * This class is self-contained and does not depend on any other plugin.
 *
 *  @package    local_edwiserlocalizer
 *  @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  @author     Harshal Thakare
 */

namespace local_edwiserlocalizer;

/**
 * Feedback sender class.
 */
class feedback_sender {

    /** @var string Edwiser feedback API endpoint. */
    const ENDPOINT = 'https://edwiser.org/wp-json/edd/v1/onboarding-data';

    /**
     * Send usage data as a user feedback payload.
     *
     * Formats the raw analytics array into a feedback-style Q&A payload
     * and POSTs it to the Edwiser onboarding endpoint.
     *
     * @param array $feedbackdata Raw usage analytics data (question => answer format).
     * @return array Response array from the server or error details.
     */
    public function send_feedback(array $feedbackdata): array {
        $payload = $this->prepare_payload($feedbackdata);
        $json = json_encode($payload);

        $curl = new \curl();
        $curl->setHeader([
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $result = $curl->post(self::ENDPOINT, $json, [
            'CURLOPT_USERAGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        ]);
        $errno = $curl->get_errno();
        $error = $curl->error;

        if ($errno !== 0) {
            return [
                'status' => false,
                'message' => $error ?: 'cURL request failed',
                'error_code' => $errno,
            ];
        }

        if (empty($result)) {
            return [
                'status' => false,
                'message' => 'Empty response from server',
            ];
        }

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return [
                'status' => false,
                'message' => 'Invalid JSON response from server',
                'raw' => $result,
            ];
        }

        return $decoded;
    }

    /**
     * Wrap raw feedback data into the Edwiser feedback payload format.
     *
     * @param array $feedbackdata Raw feedback/usage data.
     * @return array Formatted payload.
     */
    private function prepare_payload(array $feedbackdata): array {
        global $CFG, $SITE;

        // Build userfeedbacks as {question, answer} objects so the server renders them.
        $userfeedbacks = [];
        $mappings = [
            'siteurl'        => 'Site URL',
            'site_name'      => 'Site Name',
            'admin_email'    => 'Admin Email',
            'email'          => 'Support Email',
            'product_name'   => 'Product Name',
            'moodle_version' => 'Moodle Version',
        ];

        foreach ($mappings as $key => $label) {
            $userfeedbacks['edwiserlocalizer_' . $key] = [
                'question' => $label,
                'answer'   => $feedbackdata[$key] ?? '',
            ];
        }

        // Product settings is a nested array — encode as JSON string.
        $userfeedbacks['edwiserlocalizer_product_settings'] = [
            'question' => 'Product Settings',
            'answer'   => json_encode($feedbackdata['product_settings'] ?? []),
        ];

        $sitename = $feedbackdata['site_name'] ?? '';
        return [
            'customername' => $sitename,
            'email'        => $this->get_admin_email(),
            'pluginame'    => 'Edwiser Localizer',
            'licensekey'   => '',
            'siteurl'      => preg_replace('#^https?://#', '', rtrim($CFG->wwwroot, '/')),
            'responsetype' => 'userfeedbacks',
            'userfeedbacks' => $userfeedbacks,
        ];
    }

    /**
     * Get the primary admin email address.
     *
     * @return string Admin email or empty string.
     */
    private function get_admin_email(): string {
        global $CFG, $DB;

        if (!empty($CFG->siteadmins)) {
            $adminids = explode(',', $CFG->siteadmins);
            $firstadmin = $DB->get_record('user', ['id' => (int) $adminids[0], 'deleted' => 0], 'email');
            if ($firstadmin) {
                return $firstadmin->email;
            }
        }

        if (!empty($CFG->supportemail)) {
            return $CFG->supportemail;
        }

        return '';
    }
}
