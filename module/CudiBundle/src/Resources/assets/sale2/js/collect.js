(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue - F8',
        tConclude: 'Finish - F9',
        tCancel: 'Cancel - F10',
        saveComment: function (id, comment) {},
        showQueue: function () {},
        cancel: function (id) {},
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
            $(this).saleInterface('show', data);
            return this;
        },
        hide : function (data) {
            $(this).saleInterface('hide');
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
})(jQuery);