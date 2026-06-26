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

        // Choose where the icon should appear on your site.
        $name = 'local_edwisertranslate/showicon';
        $title = get_string('showicon', 'local_edwisertranslate');
        $description = get_string('showicon_desc', 'local_edwisertranslate');
        $default = 0;
        $settings->add(new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            array(
                0 => get_string('across_site', 'local_edwisertranslate'),
                1 => get_string('coursepage', 'local_edwisertranslate')
            )
        ));

        // The original language is the language in which your website is written/
        $name = 'local_edwisertranslate/translateto';
        $title = get_string('translateto', 'local_edwisertranslate');
        $description = get_string('translateto_desc', 'local_edwisertranslate');
        $default = ['en'];
        $choices =
            [
                'en'    => 'English',
                'af'    => 'Afrikaans',
                'sq'    => 'Shqip',
                'am'    => 'አማርኛ',
                'ar'    => 'العربية',
                'hy'    => 'Հայերեն',
                'az'    => 'Azərbaycan',
                'eu'    => 'Euskara',
                'be'    => 'Беларуская',
                'bn'    => 'বাংলা',
                'bs'    => 'Bosanski',
                'bg'    => 'Български',
                'ca'    => 'Català',
                'ceb'   => 'Cebuano',
                'zh_cn' => '中文（简体）',
                'zh_tw' => '中文（繁體）',
                'co'    => 'Corsu',
                'hr'    => 'Hrvatski',
                'cs'    => 'Čeština',
                'da'    => 'Dansk',
                'nl'    => 'Nederlands',
                'eo'    => 'Esperanto',
                'et'    => 'Eesti',
                'fi'    => 'Suomi',
                'fr'    => 'Français',
                'fy'    => 'Frysk',
                'gl'    => 'Galego',
                'ka'    => 'ქართული',
                'de'    => 'Deutsch',
                'el'    => 'Ελληνικά',
                'gu'    => 'ગુજરાતી',
                'ht'    => 'Kreyòl Ayisyen',
                'ha'    => 'Hausa',
                'haw'   => 'ʻŌlelo Hawaiʻi',
                'he'    => 'עברית',
                'hi'    => 'हिन्दी',
                'hmn'   => 'Hmong',
                'hu'    => 'Magyar',
                'is'    => 'Íslenska',
                'ig'    => 'Igbo',
                'id'    => 'Bahasa Indonesia',
                'ga'    => 'Gaeilge',
                'it'    => 'Italiano',
                'ja'    => '日本語',
                'jv'    => 'Basa Jawa',
                'kn'    => 'ಕನ್ನಡ',
                'kk'    => 'Қазақ',
                'km'    => 'ខ្មែរ',
                'rw'    => 'Kinyarwanda',
                'ko'    => '한국어',
                'ku'    => 'Kurdî',
                'ky'    => 'Кыргызча',
                'lo'    => 'ລາວ',
                'la'    => 'Latina',
                'lv'    => 'Latviešu',
                'lt'    => 'Lietuvių',
                'lb'    => 'Lëtzebuergesch',
                'mk'    => 'Македонски',
                'mg'    => 'Malagasy',
                'ms'    => 'Bahasa Melayu',
                'ml'    => 'മലയാളം',
                'mt'    => 'Malti',
                'mi'    => 'Te Reo Māori',
                'mr'    => 'मराठी',
                'mn'    => 'Монгол',
                'my'    => 'မြန်မာ',
                'ne'    => 'नेपाली',
                'no'    => 'Norsk',
                'ny'    => 'Chichewa',
                'or'    => 'ଓଡ଼ିଆ',
                'ps'    => 'پښتو',
                'fa'    => 'فارسی',
                'pl'    => 'Polski',
                'pt'    => 'Português',
                'pt_br' => 'Português (Brasil)',
                'pt_pt' => 'Português (Portugal)',
                'pa'    => 'ਪੰਜਾਬੀ',
                'ro'    => 'Română',
                'ru'    => 'Русский',
                'sm'    => 'Gagana Sāmoa',
                'gd'    => 'Gàidhlig',
                'sr'    => 'Српски',
                'st'    => 'Sesotho',
                'sn'    => 'Shona',
                'sd'    => 'سنڌي',
                'si'    => 'සිංහල',
                'sk'    => 'Slovenčina',
                'sl'    => 'Slovenščina',
                'so'    => 'Soomaali',
                'es'    => 'Español',
                'su'    => 'Basa Sunda',
                'sw'    => 'Kiswahili',
                'sv'    => 'Svenska',
                'tl'    => 'Tagalog',
                'tg'    => 'Тоҷикӣ',
                'ta'    => 'தமிழ்',
                'tt'    => 'Татар',
                'te'    => 'తెలుగు',
                'th'    => 'ไทย',
                'tr'    => 'Türkçe',
                'tk'    => 'Türkmen',
                'uk'    => 'Українська',
                'ur'    => 'اردو',
                'ug'    => 'ئۇيغۇرچە',
                'uz'    => 'Oʻzbek',
                'vi'    => 'Tiếng Việt',
                'cy'    => 'Cymraeg',
                'xh'    => 'isiXhosa',
                'yi'    => 'ייִדיש',
                'yo'    => 'Yorùbá',
                'zu'    => 'isiZulu',
            ];
        $settings->add(new \local_edwisertranslate\translation_auto_complete(
            $name,
            $title,
            $description,
            $default,
            $choices,
            [
                'multiple'        => true,
                'tags'            => false,
                'showsuggestions' => true,
                'placeholder'     => get_string('chooselanguages', 'local_edwisertranslate'),
                'manageurl'       => false,
                'managetext'      => false,
            ]
        ));


        // Choose the appearance of the language switcher to match your site.
        $name = 'local_edwisertranslate/appearance';
        $title = get_string('appearance', 'local_edwisertranslate');
        $description = get_string('appearance_desc', 'local_edwisertranslate');
        $default = 0;
        $settings->add(new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            array(
                0 => get_string('light', 'local_edwisertranslate'),
                1 => get_string('dark', 'local_edwisertranslate')
            )
        ));
        // Usage tracking GDPR setting.
        $name = 'local_edwisertranslate/enableusagetracking';
        $title = get_string('enableusagetracking', 'local_edwisertranslate');
        $description = get_string('enableusagetrackingdesc', 'local_edwisertranslate');
        $default = true;
        $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));
    }
    $ADMIN->add('localplugins', $settings);
}
