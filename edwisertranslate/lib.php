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
 * Callback implementations for Edwiser Language Translation
 *
 * @package    local_edwiserlanguagetranslation
 * @copyright  2026 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// plugin should display or not based on show icon
function local_edwisertranslate_should_display(): bool
{
    global $PAGE;
    // plugin is on/off
    $isenabled = get_config('local_edwisertranslate', 'enable');
    if ($isenabled == 0) {
        return false;
    }

    // plugin is set across the site or course page
    $scope = get_config('local_edwisertranslate', 'showicon');
    if ($scope == 0) {
        return true;
    }
    if ($scope == 1) {
        if ($PAGE->context->contextlevel == CONTEXT_COURSE || $PAGE->course->id > SITEID) {
            return true;
        }
    }
    return false;
}

//navbar
function local_edwisertranslate_render_navbar_output(): string
{
    global $PAGE, $OUTPUT;
    $placement = get_config('local_edwisertranslate', 'placement');

    //header is 0
    if ($placement == 1) {
        return '';
    }

    if (!local_edwisertranslate_should_display()) {
        return '';
    }

    $context = [
        'is_footer' => false,
        'appearance' => get_config('local_edwisertranslate', 'appearance')

    ];

    //js module
    $PAGE->requires->js_call_amd('local_edwisertranslate/navbar', 'init', [$context]);

    // output to mustache file
    return $OUTPUT->render_from_template('local_edwisertranslate/lang_switcher', $context);
}

