(function ($) {
    var defaults = {
        closable: false
    };

    var methods = {
        open: function (options) {
            options = $.extend(defaults, options);

            $(this).find('.modal-header .close').toggle(options.closable);

            $(this).modal({
                keyboard: options.closable,
                backdrop: options.closable ? true : 'static',
            }).modal('show');

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
            $.error('Method ' +  method + ' does not exist on $.formUploadProgress');
        }
    };
}) (jQuery);