(function ($) {
    var defaults = {
        closable: false
    };

    var methods = {
        open: function (options) {
            options = $.extend(defaults, options);

            $(this).find('.modal-header .close').toggle(options.closable);

            if ($(this).data('modal')) {
                $(this).data('modal').options.keyboard = options.closable;
                $(this).data('modal').options.backdrop = options.closable ? true : 'static';
                $(this).modal();
            } else {
                $(this).modal({
                    keyboard: options.closable,
                    backdrop: options.closable ? true : 'static',
                });
            }

            return this;
        },
        hide: function () {
            $(this).modal('hide');

            return this;
        }
    };

    $.fn.permanentModal = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.open.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.permanentModal');
        }
    };
}) (jQuery);