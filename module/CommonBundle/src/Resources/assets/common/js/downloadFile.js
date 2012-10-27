(function ($) {
    $.fn.downloadFile = function (file, completed) {
        $(this).click(function (e) {
            e.preventDefault();

            $('iframe.downloadFile').remove();
            var iframe = $('<iframe>', {src: file, class: 'export', css: {display: 'none'}}).appendTo('body')
        });
        return this;
    }
})(jQuery);