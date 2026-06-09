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
        global $PAGE, $OUTPUT;

        // footer is 1
        $placement = get_config('local_edwisertranslate', 'placement');
        if ($placement != 1) {
            return;
        }

        // check weather it should display
        if (!local_edwisertranslate_should_display()) {
            return;
        }

        // Prepare context 
        $context = [
            'is_footer' => true,
            'appearance' => get_config('local_edwisertranslate', 'appearance'),
            'languages' => local_edwisertranslate_get_language(),
            'currentlang' => strtoupper(current_language())
        ];

        $PAGE->requires->js_call_amd('local_edwisertranslate/navbar', 'init', [$context]);

        $html = $OUTPUT->render_from_template('local_edwisertranslate/lang_switcher', $context);
        $hook->add_html($html);
    }
}
