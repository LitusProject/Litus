(function ($) {
    $(document).ready(function () {
        $('.controller_sub_header').subnavCollapse();

        $(window).resize(function () {
            $('.controller_sub_header').subnavCollapse('update');
        });
    });

    var methods = {
        init: function () {
            this.each(function () {
                _init($(this));
            });
            return this;
        },
        update: function () {
            this.each(function () {
                _update($(this));
            });
            return this;
        }
    };

    $.fn.subnavCollapse = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.fn.subnavCollapse');
        }
    };

    function _init($this) {
        var caret = $('<div>', {'class': 'caret_sub_nav'}).html('&raquo;').hide();
        $this.append(caret);

        caret.unbind('click').click(function () {
            if ($(this).data('shown')) {
                $('.sub_nav_dropdown').remove();
                $(this).data('shown', false);
                $(this).removeClass('active');
            } else {
                $('.sub_nav_dropdown').remove();
                $(this).data('shown', true);
                $(this).addClass('active');
                var dropdown = $('<div>', {'class': 'sub_nav_dropdown'});
                $(this).parent().find('li:not(:visible)').each(function () {
                    dropdown.append($(this).find('a').clone());
                });
                dropdown.css({
                    'top': caret.offset().top + caret.outerHeight(),
                    'right': $(window).width() - caret.offset().left - caret.outerWidth(),
                });
                $('body').append(dropdown);
            }
        });

        _update($this);
    }

    function _update($this) {
        $('.sub_nav_dropdown').remove();
        $('.caret_sub_nav').data('shown', false).removeClass('active');

        var totalWidth = 0;
        $this.find('li').each(function () {
            $(this).show();
            totalWidth += $(this).outerWidth();

            if (totalWidth > $this.width()) {
                $(this).hide();
            }
        });

        $this.find('.caret_sub_nav').toggle(totalWidth > $this.width());
    }
})(jQuery);