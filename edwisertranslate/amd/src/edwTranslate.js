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

define(['jquery'], function ($) {
    return {
        init: function (config) {
            var $wrapper = $('#local-translator-wrapper');
            if (!$wrapper.length) {
                return;
            }

            if (config.theme_name == "remui") {
                // console.log("remui is the theme "+config.theme_name);
                $("body").addClass("edw-translate");
            }

            if (config.is_footer) {
                $wrapper.addClass('dropup').removeClass('dropdown');
                if (config.theme_name !== "remui") {
                    var $notRemuiFooter = $('#page-footer');
                    $wrapper.prependTo($notRemuiFooter);
                }
                else {
                    var $footer = $('#page-footer, #footer-column-1').last();
                    if ($footer.length) {
                        $footer.append($wrapper);
                    }
                }
            }
            else {
                $wrapper.addClass('dropdown').removeClass('dropup');
            }

            /**
             * Helper function to update the text inside the spans
             */
            function updateDynamicText() {
                var $darkmode = $('#usernavigation .nav-darkmode');
                var $editmode = $('#usernavigation .editmode-switch-form');

                // Update dark mode text (Light/Dark)
                var $dmSpan = $darkmode.find('.dm-mobile-text');
                if ($dmSpan.length) {
                    // Check if dark mode is active
                    var isDarkActive = $darkmode.hasClass('enabled');
                    $dmSpan.text(isDarkActive ? config.str_light : config.str_dark);
                }

                // Update Edit Mode Text (On/Off)
                var $emSpan = $editmode.find('.em-mobile-text');
                if ($emSpan.length) {
                    var isEditing = $editmode.find('input[type="checkbox"]').is(':checked');
                    $emSpan.text(isEditing ? config.str_on : config.str_off);
                }
            }

            /**
             * this function checks the screen size and then remove
             * the d none class from dropdown and adds edit mode and dark mode to dropdown
             */
            function updateDarkmodeMenu() {
                if (config.is_footer) {
                    return;
                }

                const $darkmode = $('#usernavigation .nav-darkmode');
                const $editmode = $('#usernavigation .editmode-switch-form');
                const $dropdown = $('#usernavigation .darkmode-mobile-dropdown');
                const $mobileMenu = $('#usernavigation #darkmode-mobile-menu');

                if (!$darkmode.length || !$editmode.length || !$dropdown.length) {
                    return;
                }

                const $preset = $('#usernavigation .preset-nav-group').last();
                if ($(window).width() <= 426) {
                    $preset.addClass('d-none');
                } else {
                    $preset.removeClass('d-none');
                }

                // MOBILE VIEW
                if ($(window).width() <= 426) {
                    $dropdown.removeClass('d-none');

                    // Dark mode add text
                    if (!$mobileMenu.find('.nav-darkmode').length) {
                        $mobileMenu.append($darkmode);
                    }
                    if (!$darkmode.find('.dm-mobile-text').length) {
                        $darkmode
                        .append('<span class="dm-mobile-text ml-2 font-weight-bold" style="font-size: 16px;"></span>');
                    }

                    // Edit Mode add text
                    if (!$mobileMenu.find('.editmode-switch-form').length) {
                        $mobileMenu.append($editmode);
                    }
                    if (!$editmode.find('.em-mobile-text').length) {
                        $editmode
                        .append(
                        '<span class="em-mobile-text ml-2 font-weight-bold align-self-center" style="font-size: 16px;"></span>');
                    }

                    // Set the initial text correctly
                    updateDynamicText();

                }
                // Desktop view
                else {
                    $dropdown.addClass('d-none');

                    const $targetParent = $('#usernavigation .preset-nav-group').last();

                    if ($targetParent.length) {
                        const $targetLi = $('#usernavigation .preset-nav-group').last().find('li').first();

                        // Move elements back and remove the mobile text spans
                        if (!$targetParent.find('.editmode-switch-form').length) {
                            $editmode.appendTo($targetLi);
                            $editmode.find('.em-mobile-text').remove();
                        }

                        if (!$targetParent.find('.nav-darkmode').length) {
                            $darkmode.prependTo($targetParent);
                            $darkmode.find('.dm-mobile-text').remove();
                        }
                    }
                }
            }

            updateDarkmodeMenu();
            $(window).on('resize', updateDarkmodeMenu);

            // Listen for clicks/changes to dynamically update the text while the menu is open
            $('#usernavigation').on('click', '.nav-darkmode', function () {
                // timeout ensures Moodle updates
                setTimeout(updateDynamicText, 100);
            });

            $('#usernavigation').on('change', '.editmode-switch-form input[type="checkbox"]', function () {
                updateDynamicText();
            });

    // ============================================
    // GOOGLE TRANSLATE INTEGRATION
    // ============================================

    var pageLanguage = config.currentlang_gtcode || 'en';

    var SELECTORS = {
        CURRENT: '.selected-language',
        LANG_ITEM: '.lang-switch-link'
    };

    /**
     * Load Google Translate Element script and initialize hidden translator.
     */
    function loadGoogleTranslate() {
        window.googleTranslateElementInit2 = function () {
            new window.google.translate.TranslateElement({
                pageLanguage: pageLanguage,
                autoDisplay: false
            }, 'google_translate_element2');
        };

        var script = document.createElement('script');
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2';
        script.async = true;
        document.head.appendChild(script);
    }

    /**
     * Trigger Google Translate to switch to a target language.
     * Sets the googtrans cookie and reloads the page so Google
     * Translate activates on the next load.
     *
     * @param {string} langPair e.g. "en|fr"
     */
    function doTranslate(langPair) {
        if (langPair === '') {
            return;
        }
        var parts = langPair.split('|');
        var sourceLang = parts[0];
        var targetLang = parts[1];

        if (sourceLang === targetLang) {
            clearGoogTransCookie();
        } else {
            setGoogTransCookie('/' + sourceLang + '/' + targetLang);
        }
        window.location.reload();
    }

    /**
     * Set the googtrans cookie to activate Google Translate.
     *
     * @param {string} value e.g. "/en/fr"
     */
    function setGoogTransCookie(value) {
        document.cookie = 'googtrans=' + value + ';path=/';
        document.cookie = 'googtrans=' + value + ';path=/;domain=' + window.location.hostname;
    }

    /**
     * Clear the googtrans cookie to restore original language.
     */
    function clearGoogTransCookie() {
        document.cookie = 'googtrans=;path=/;expires=Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'googtrans=;path=/;domain=' + window.location.hostname +
            ';expires=Thu, 01 Jan 1970 00:00:00 GMT';
    }

    /**
     * Get current target language from the googtrans cookie.
     *
     * @returns {string|null} Target language code or null.
     */
    function getCurrentGoogTransLang() {
        var match = document.cookie.match(/googtrans=\/[^/]+\/([^;]+)/);
        return match ? match[1] : null;
    }

    /**
     * Hide Google Translate top banner and injected UI elements.
     */
    function hideGoogleBranding() {
        var css = document.createElement('style');
        css.textContent = [
            '.goog-te-banner-frame { display: none !important; }',
            '#goog-gt-tt { display: none !important; }',
            '.goog-te-balloon-frame { display: none !important; }',
            '.goog-tooltip { display: none !important; }',
            '.goog-tooltip:hover { display: none !important; }',
            '.goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }',
            '#google_translate_element2 { display: none !important; }',
            '.goog-te-combo { display: none !important; }',
            'body { top: 0 !important; }',
            '.skiptranslate { display: none !important; }'
        ].join('\n');
        document.head.appendChild(css);
    }

    /**
     * Update the dropdown label and active state for the selected language.
     *
     * @param {string} langCode Moodle language code.
     */
    function updateCurrentLabel(langCode) {
        $(SELECTORS.CURRENT).text(langCode.toUpperCase());
        $(SELECTORS.LANG_ITEM).removeClass('active');
        $(SELECTORS.LANG_ITEM + "[data-lang-code='" + langCode + "']").addClass('active');
    }

    /**
     * Initialize Google Translate integration.
     * Loads the hidden translator, wires click handlers,
     * and restores any previously selected language.
     */
    function initGoogleTranslate() {
        hideGoogleBranding();
        loadGoogleTranslate();

        $(document).on('click', SELECTORS.LANG_ITEM, function (e) {
            e.preventDefault();
            var $item = $(this);
            var langCode = $item.data('lang-code');
            var gtLang = $item.data('gtlang');

            updateCurrentLabel(langCode);
            doTranslate(pageLanguage + '|' + gtLang);
        });

        var currentGtLang = getCurrentGoogTransLang();
        if (currentGtLang && currentGtLang !== pageLanguage) {
            var $activeLang = $(SELECTORS.LANG_ITEM + "[data-gtlang='" + currentGtLang + "']");
            if ($activeLang.length) {
                updateCurrentLabel($activeLang.data('lang-code'));
            }
        }
    }

    initGoogleTranslate();
}
    };
});
