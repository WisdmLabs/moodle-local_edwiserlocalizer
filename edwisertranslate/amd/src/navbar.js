define(['jquery'], function($) {
    return {
        init: function(config) {
            var $wrapper = $('#local-translator-wrapper');
            if (!$wrapper.length) {
                return;
            }

            if (config.is_footer) {
                $wrapper.addClass('dropup').removeClass('dropdown');
                var $footer = $('footer, #footer-column-1').last();
                if ($footer.length) {
                    // console.log("it is working");
                    $footer.append($wrapper);
                }
            } 
            else{
                $wrapper.addClass('dropdown').removeClass('dropup');
            }
        }
    };
});