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
                    var $footer = $('footer, #footer-column-1').last();
                    if ($footer.length) {
                        $footer.append($wrapper);
                    }
                }
            }
            else {
                $wrapper.addClass('dropdown').removeClass('dropup');
            }

            /**
             * this function checks the screen size and then remove
             *  the d none class from dropdown and adds edit mode and dark mode to dropdown
             */
            function updateDarkmodeMenu() {

                const $darkmode = $('#usernavigation .nav-darkmode');
                const $editmode = $('#usernavigation .editmode-switch-form');
                const $dropdown = $('#usernavigation .darkmode-mobile-dropdown');
                const $mobileMenu = $('#usernavigation #darkmode-mobile-menu');

                if (!$darkmode.length || !$editmode.length || !$dropdown.length) {
                    return;
                }

                if ($(window).width() <= 375) {

                    $dropdown.removeClass('d-none');

                    if (!$mobileMenu.find('.nav-darkmode').length) {
                        $mobileMenu.append($darkmode);
                    }

                    if (!$mobileMenu.find('.editmode-switch-form').length) {
                        $mobileMenu.append($editmode);
                    }

                } else {

                    $dropdown.addClass('d-none');

                    const $targetParent = $('#usernavigation .preset-nav-group').last();

                    if ($targetParent.length) {

                        const $targetLi = $('#usernavigation .preset-nav-group').last().find('li').first();
                        if (!$targetParent.find('.editmode-switch-form').length) {
                            $editmode.appendTo($targetLi);
                        }

                        if (!$targetParent.find('.nav-darkmode').length) {
                            $darkmode.prependTo($targetParent);
                        }
                    }
                }
            }

            updateDarkmodeMenu();
            $(window).on('resize', updateDarkmodeMenu);
        }
    };
});