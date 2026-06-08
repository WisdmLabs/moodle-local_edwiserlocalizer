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
 * TODO describe file settings
 *
 * @package    local_edwiserlanguagetranslation
 * @copyright  2026 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Harshal Thakare
 */

defined('MOODLE_INTERNAL') || die();



$pluginname = get_string('pluginname', 'local_edwisertranslate');

if ($hassiteconfig) {

    // Create plugin category under Local plugins.
    $ADMIN->add(
        'localplugins',
        new admin_category(
            'local_edwisertranslate_settings',
            get_string('pluginname', 'local_edwisertranslate')
        )
    );

    // Create settings page.
    $settings = new admin_settingpage(
        'local_edwisertranslate',
        get_string('pluginname', 'local_edwisertranslate')
    );

    if ($ADMIN->fulltree) {

        // Enabling this will display the language translation on your site.
        $name = 'local_edwisertranslate/enable';
        $title = get_string('enable', 'local_edwisertranslate');
        $description = get_string('enable_desc', 'local_edwisertranslate');
        $default = true;
        $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));

        // Choose where the icon should appear on your site.
        $name = 'local_edwisertranslate/placement';
        $title = get_string('placement', 'local_edwisertranslate');
        $description = get_string('placement_desc', 'local_edwisertranslate');
        $default = 0;
        $settings->add(new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            array(
                0 => get_string('header', 'local_edwisertranslate'),
                1 => get_string('footer', 'local_edwisertranslate')
            )
        ));
    }
    $ADMIN->add('localplugins', $settings);
}
