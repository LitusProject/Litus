(function ($) {
    var defaults = {
    };

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);
            $(this).data('saleSettings', settings);

            _init($this);
            return this;
        }
    };

    $.fn.sale = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.sale');
        }
    };

    $.sale = function (options) {
        return $('<div>').sale(options);
    }

    function _init($this) {
        var settings = $this.data('saleSettings');
    }
})(jQuery);