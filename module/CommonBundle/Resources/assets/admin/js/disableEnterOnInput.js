(function ($) {
    $(document).ready(function () {
        $('input.disableEnter').keydown(function (e) {
            if (e.which == 13)
                e.preventDefault();
        });
    });
}) (jQuery);