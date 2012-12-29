(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Conclude',
        tCancel: 'Cancel',

        discounts: [],

        saveComment: function (id, comment) {},
        showQueue: function () {},
        finish: function (id, articles) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status},
        addArticle: function (id, barcode) {},
    };

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            settings.isSell = true;
            settings.conclude = settings.finish;

            var $this = $(this);
            $(this).data('saleSettings', settings);

            return this;
        },
        show : function (data) {
            currentView = 'sale';
            $(this).saleInterface('show', $(this).data('saleSettings'), data);
            return this;
        },
        hide : function (data) {
            $(this).saleInterface('hide');
            return this;
        },
        gotBarcode : function (barcode) {
            $(this).saleInterface('gotBarcode', barcode);
            return this;
        },
        addArticle : function (data) {
            $(this).saleInterface('addArticle', data);
            return this;
        },
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
})(jQuery);