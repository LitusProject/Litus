(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComment: 'Comment',
        tQueue: 'Queue - F8',
        tConclude: 'Finish - F9',
        tCancel: 'Cancel - F10',
    };

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            $(this).saleInterface($.extend({isSell: false}, settings));

            var $this = $(this);
            $(this).data('collectSettings', settings);

            _init($this);
            return this;
        },
        show : function (data) {
            currentView = 'collect';
            _show($(this), data);
            return this;
        },
    };

    $.fn.collect = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.collect');
        }
    };

    function _init($this) {
        var settings = $this.data('collectSettings');
    }

    function _show($this, data) {
        var settings = $this.data('collectSettings');

        $this.saleInterface('show', data);
    }
})(jQuery);