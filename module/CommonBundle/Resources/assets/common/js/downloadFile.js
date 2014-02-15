(function ($) {
    $.fn.downloadFile = function (file) {
        $(this).click(function (e) {
            e.preventDefault();

            $('iframe.downloadFile').remove();
            var iframe = $('<iframe>', {'src': file, 'class': 'export', 'css': {'display': 'none'}}).appendTo('body');
        });
        return this;
    }

    $(document).ready(function () {
        $('[data-provide="downloadFile"]').each(function () {
            $(this).downloadFile($(this).data('url'));
        });
    });
})(jQuery);