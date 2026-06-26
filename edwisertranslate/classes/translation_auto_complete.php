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
 * TODO describe file translationautocomplete
 *
 * @package    core
 * @copyright  2026 Harshal <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwisertranslate;

use core_admin\local\settings\autocomplete;

class translation_auto_complete extends autocomplete
{
    public function write_setting($data)
    {
        if (!is_array($data)) {
            return ''; // Ignore it.
        }
        if (!$this->load_choices() || empty($this->choices)) {
            return '';
        }

        unset($data['xxxxx']);

        $save = [];
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // Ignore it.
            }
            $save[] = $value;
        }
        if (empty($save)) {
            // Return the error string it shows a UI error.
            return get_string('error_nolanguage', 'local_edwisertranslate');
        }

        return ($this->config_write($this->name, implode($this->delimiter, $save)) ? '' : get_string('errorsetting', 'admin'));
    }
}
