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
 * @package    local_edwisertranslate
 * @copyright  2026 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// plugin should display or not based on show icon
function local_edwisertranslate_should_display(): bool
{
    global $PAGE;
    // it stops the page from populating the button on customizer.php page
    if ($PAGE->url && strpos($PAGE->url->out(false), 'theme/remui/customizer.php') !== false) {
        return false;
    }
    // plugin is on/off
    $isenabled = get_config('local_edwisertranslate', 'enable');
    if (!$isenabled) {
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

/**
 * Map a Moodle language code to its Google Translate language code.
 *
 * @param string $moodlecode Moodle language code (e.g. 'zh_cn').
 * @return string Google Translate language code (e.g. 'zh-CN').
 */
function local_edwisertranslate_get_gtcode(string $moodlecode): string
{
    $map = [
        'zh_cn' => 'zh-CN',
        'zh_tw' => 'zh-TW',
        'pt_br' => 'pt-BR',
        'pt_pt' => 'pt-PT',
        'he'    => 'iw',
    ];
    return $map[$moodlecode] ?? $moodlecode;
}

//navbar
function local_edwisertranslate_render_navbar_output(): string
{
    global $PAGE, $OUTPUT;
    // Usage tracking - runs for admins on every page load.
    if (is_siteadmin()) {
        $tracker = new \local_edwisertranslate\usage_tracking();
        $tracker->send_usage_analytics();
    }
    $placement = get_config('local_edwisertranslate', 'placement');

    //header is 0
    if ($placement == 1) {
        return '';
    }

    if (!local_edwisertranslate_should_display()) {
        return '';
    }

    $currentlang = current_language();
    $context = [
        'is_footer' => false,
        'appearance' => get_config('local_edwisertranslate', 'appearance'),
        'languages' => local_edwisertranslate_get_language(),
        'currentlang' => strtoupper($currentlang),
        'currentlang_gtcode' => local_edwisertranslate_get_gtcode($currentlang),
        //js context
        'jsconfig' => json_encode([
            'is_footer' => false,
            'appearance' => get_config('local_edwisertranslate', 'appearance'),
            'theme_name' => $PAGE->theme->name,
            'str_light' => get_string('light', 'local_edwisertranslate'),
            'str_dark'  => get_string('dark', 'local_edwisertranslate'),
            'str_on'    => get_string('on', 'local_edwisertranslate'),
            'str_off'   => get_string('off', 'local_edwisertranslate'),
            'currentlang_gtcode' => local_edwisertranslate_get_gtcode($currentlang)
        ])
    ];

    // output to mustache file
    return $OUTPUT->render_from_template('local_edwisertranslate/lang_switcher', $context);
}

function local_edwisertranslate_get_language(): array
{
    global $PAGE;

    $userChossenLang = get_config('local_edwisertranslate', 'translateto');
    // if no lang choosen add english
    if (empty($userChossenLang)) {
        $userChossenLang = 'en';
    }

    $langCodes = explode(',', $userChossenLang);

    //define lang array
    $available_lang =
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

    $language_context = [];
    $currentlang = current_language();
    foreach ($langCodes as $code) {
        $code = trim($code);

        $language_context[] = [
            'code' => $code,
            'name' => $available_lang[$code],
            'gtcode' => local_edwisertranslate_get_gtcode($code),
            'isactive' => ($code === $currentlang),
            'switchurl' => (new moodle_url($PAGE->url, ['lang' => $code]))->out(false)
        ];
    }
    return $language_context;
}
