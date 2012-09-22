(function ($) {
    $(document).ready(function () {
        var totalWidth = 0;
        var maxWidth = $('#controller_sub_header').width();
        $('#supplier_nav').css('position', 'relative');
        $('#supplier_nav li').each(function () {
            $(this).css(
                {
                    position: 'absolute',
                    top: 0,
                    left: totalWidth,
                    whiteSpace: 'nowrap',
                }
            );
            totalWidth += $(this).outerWidth(true) + 3;
        });
        if (totalWidth > maxWidth) {
            offset = maxWidth / 2 - $('#supplier_nav li a.active').parent().position().left;
            $('#supplier_nav').css('left', Math.min(Math.max(offset, maxWidth - totalWidth), 0));
        }
    });
}) (jQuery)