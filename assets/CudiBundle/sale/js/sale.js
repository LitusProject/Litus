(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Conclude',
        tCancel: 'Cancel',
        tConcludeSale: 'Conclude Sale',
        tSell: 'Sell',
        tClose: 'Close',
        tCalculateChange: 'Calculate Change',
        tPayed: 'Payed',
        tChange: 'Change',
        tPaymentMethod: 'Payment Method',
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
                $('<div>', {'class': 'modal-dialog modal-kg'}).append(
                    $('<div>', {'class': 'modal-content'}).append(
                        $('<div>', {'class': 'modal-header'}).append(
                            $('<h5>', {'class': 'modal-title'}).html(settings.tConcludeSale),
                            $('<button>', {'type': 'button', 'class': 'close', 'data-dismiss': 'modal'}).append(
                                $('<span>').html('&times;')
                            )
                        ),
                        $('<div>', {'class': 'modal-body'}).append(
                            $('<h6>', {'class': 'mb-1'}).html(settings.tCalculateChange),
                            $('<div>', {'class': 'form-row mb-3'}).append(
                                $('<div>', {'class': 'form-group col-6'}).append(
                                    $('<label>', {'for': 'payedMoney'}).html(settings.tPayed),
                                    $('<div>', {'class': 'input-group'}).append(
                                        $('<div>', {'class': 'input-group-prepend'}).append(
                                            $('<div>', {'class': 'input-group-text'}).html('&euro;')
                                        ),
                                        payed = $('<input>', {'type': 'text', 'class': 'form-control', 'id': 'payedMoney'}).val('0.00')
                                    )
                                ),
                                $('<div>', {'class': 'form-group col-6'}).append(
                                    $('<label>', {'for': 'changeMoney'}).html(settings.tChange),
                                    $('<div>', {'class': 'input-group'}).append(
                                        $('<div>', {'class': 'input-group-prepend'}).append(
                                            $('<div>', {'class': 'input-group-text'}).html('&euro;')
                                        ),
                                        change = $('<input>', {'type': 'text', 'class': 'form-control', 'id': 'changeMoney'}).prop('readonly', true).val('0.00')
                                    )
                                )
                            ),
                            $('<h6>', {'class': 'mb-1'}).html(settings.tPaymentMethod),
                            method = $('<div>', {'class': 'btn-group btn-group-toggle', 'data-toggle': 'buttons'}).append(
                                $('<label>', {'class': 'btn btn-secondary active', 'data-key': 114, 'data-method': 'cash'}).append(
                                    $('<input>', {'type': 'radio'}).prop('checked', true),
                                    (settings.tCash + ' - F3')
                                ),
                                $('<label>', {'class': 'btn btn-secondary', 'data-key': 115, 'data-method': 'bank'}).append(
                                    $('<input>', {'type': 'radio'}),
                                    (settings.tBank + ' - F4')
                                )
                            )
                        ),
                        $('<div>', {'class': 'modal-footer'}).append(
                            $('<button>', {'class': 'btn btn-secondary', 'data-dismiss': 'modal'}).html(settings.tClose),
                            $('<button>', {'class': 'btn btn-success', 'data-key': 122}).html(settings.tSell + ' - F11').click(function () {
                                settings.finish(id, articles, $this.saleInterface('getSelectedDiscounts'), method.find('button.active').data('method'));
                                $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
                                    payed.calculateChange('destroy');
                                    $(this).remove();
                                });
                            })
                        )
                    )
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
