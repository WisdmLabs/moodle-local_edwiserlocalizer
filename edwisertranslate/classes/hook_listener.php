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

namespace local_edwisertranslate;

defined('MOODLE_INTERNAL') || die();

class hook_listener
{
    public static function before_footer_html_generation(\core\hook\output\before_footer_html_generation $hook): void
    {
        global $PAGE, $OUTPUT, $CFG;
        require_once($CFG->dirroot . '/local/edwisertranslate/lib.php');
        // footer is 1
        $placement = get_config('local_edwisertranslate', 'placement');
        if ($placement != 1) {
            return;
        }
        // it stops the page from populating the button on home.php page when button should be strickly shown on the course page
        if ($PAGE->url && strpos($PAGE->url->out(false), '?redirect=0') !== false) {
            return;
        }
        // check weather it should display
        if (!\local_edwisertranslate_should_display()) {
            return;
        }

        // Prepare context
        $currentlang = current_language();
        $context = [
            'is_footer' => true,
            'appearance' => get_config('local_edwisertranslate', 'appearance'),
            'languages' => \local_edwisertranslate_get_language(),
            'currentlang' => strtoupper($currentlang),
            'currentlang_gtcode' => \local_edwisertranslate_get_gtcode($currentlang),
            //js context
            'jsconfig' => json_encode([
                'is_footer' => true,
                'appearance' => get_config('local_edwisertranslate', 'appearance'),
                'theme_name' => $PAGE->theme->name,
                'currentlang_gtcode' => \local_edwisertranslate_get_gtcode($currentlang),
                'str_light' => get_string('light', 'local_edwisertranslate'),
                'str_dark'  => get_string('dark', 'local_edwisertranslate'),
                'str_on'    => get_string('on', 'local_edwisertranslate'),
                'str_off'   => get_string('off', 'local_edwisertranslate'),
		'dropdown_textColor' => get_config('theme_remui', 'themecolors-textcolor'),
                'notranslate_selectors' => \local_edwisertranslate_get_notranslate_selectors()
            ])
        ];

        $html = $OUTPUT->render_from_template('local_edwisertranslate/lang_switcher', $context);
        $hook->add_html($html);
    }
}
