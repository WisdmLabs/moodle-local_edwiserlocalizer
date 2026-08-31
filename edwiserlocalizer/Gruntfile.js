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
 *  @package    local_edwiserlocalizer
 *  @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  @author     Harshal Thakare
 */

const sass = require('node-sass');
module.exports = function(grunt) {
    grunt.initConfig({
        sass: {
            dist: {
                options: {
                    implementation: sass,
                    sourcemap: false,
                    compress: false,
                    yuicompress: false,
                    style: 'expanded',
                },
                files: {
                    'styles.css' : 'scss/styles.scss'
                }
            },
        }
    });
    grunt.loadNpmTasks('grunt-sass');
    grunt.registerTask('default',['sass']);
}
