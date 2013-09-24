(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Conclude',
        tCancel: 'Cancel',
        tConfirmSale: 'Confirm Sale',
        tSell: 'Sell',
        tClose: 'Close',
        tConfirmText: 'Do you want to confirm the sale? Please note this cannot be undone.',
        tCalculateChange: 'Calculate Change:',
        tPayed: 'Payed:',
        tChange: 'Change:',
        tPayMethod: 'Pay Method:',
        tCash: 'Cash',
        tBank: 'Bank',

        discounts: [],
        articleTypeahead: '',
        membershipArticles: [{'id': 0, 'barcode': 0, 'title': '', 'price': 0}],
        lightVersion: false,

        saveComment: function (id, comment) {},
        showQueue: function () {},
        finish: function (id, articles, discounts, payMethod) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status;},
        addArticle: function (id, articleId) {},
    };

    var methods = {
        init : function (options) {

            membershipArticles = [{'id': 5, 'barcode': 10}, {'id': 7, 'barcode': 90}];

            var settings = $.extend(defaults, options);
            settings.isSell = true;
            settings.conclude = function (id, articles) {
                _finish($this, id, articles);
            };

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

    function _finish($this, id, articles) {
        var settings = $this.data('saleSettings');

        $('body').append(
            modal = $('<div>', {'class': 'modal fade'}).append(
                $('<div>', {'class': 'modal-header'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {
                        $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
                            $(this).remove();
                        });
                    }),
                    $('<h3>').html(settings.tConfirmSale)
                ),
                $('<div>', {'class': 'modal-body'}).append(
                    $('<p>').html(settings.tConfirmText),
                    $('<h4>').html(settings.tCalculateChange),
                    $('<form>', {'class': 'form-horizontal'}).append(
                        $('<div>', {'class': 'control-group'}).append(
                            $('<label>', {'class': 'control-label', 'for': 'payedMoney'}).html(settings.tPayed),
                            $('<div>', {'class': 'controls'}).append(
                                $('<div>', {'class': 'input-prepend'}).append(
                                    $('<span>', {'class': 'add-on'}).html('&euro;'),
                                    payed = $('<input>', {'class': 'input-large', 'id': 'payedMoney', 'type': 'text'}).val('0.00')
                                )
                            )
                        ),
                        $('<div>', {'class': 'control-group'}).append(
                            $('<label>', {'class': 'control-label'}).html(settings.tChange),
                            $('<div>', {'class': 'controls'}).append(
                                $('<div>', {'class': 'input-prepend'}).append(
                                    $('<span>', {'class': 'add-on'}).html('&euro;'),
                                    change = $('<input>', {'class': 'input-large uneditable-input', 'type': 'text'}).val('0.00')
                                )
                            )
                        ),
                        $('<div>', {'class': 'control-group'}).append(
                            $('<label>', {'class': 'control-label'}).html(settings.tPayMethod),
                            $('<div>', {'class': 'controls'}).append(
                                method = $('<div>', {'class': 'btn-group', 'data-toggle': 'buttons-radio'}).append(
                                    $('<button>', {'class': 'btn active', 'type': 'button', 'data-key': '114', 'data-method': 'cash'}).html(settings.tCash + ' - F3'),
                                    $('<button>', {'class': 'btn', 'type': 'button', 'data-key': '115', 'data-method': 'bank'}).html(settings.tBank + ' - F4')
                                )
                            )
                        )
                    )
                ),
                $('<div>', {'class': 'modal-footer'}).append(
                    $('<button>', {'class': 'btn btn-success', 'data-key': '122'}).html(settings.tSell + ' - F11').click(function () {
                        settings.finish(id, articles, $this.saleInterface('getSelectedDiscounts'), method.find(' button.active').data('method'));
                        $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
                            payed.calculateChange('destroy');
                            $(this).remove();
                        });
                    }),
                    $('<button>', {'class': 'btn'}).html(settings.tClose).click(function () {
                        $(this).closest('.modal').modal('hide').on('hidden', function () {
                            $(this).remove();
                        });
                    })
                )
            )
        );

        modal.modal();
        payed.focus();
        payed.calculateChange({
            changeField: change,
            totalMoney: $this.saleInterface('getTotalPrice'),
        });
    }
})(jQuery);