(function ($) {
    var defaults = {
        isSell: true,
        discounts: [],

        tCurrentCustomer: 'Current Customer',
        tComment: 'Comment',
        tQueue: 'Queue - F8',
        tConclude: 'Finish - F9',
        tCancel: 'Cancel - F10',
        tBarcode: 'Barcode',
        tTitle: 'Title',
        tStatus: 'Status',
        tNumber: 'Number',
        tPrice: 'Price',
        tActions: 'Actions',
    };

    var firstAction = true;
    var queue = null;

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);
            $(this).data('saleInterfaceSettings', settings);

            _init($this);
            return this;
        },
        show : function (data) {
            _show($(this), data);
            return this;
        }
    };

    $.fn.saleInterface = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.saleInterface');
        }
    };

    function _init($this) {
        var settings = $this.data('saleInterfaceSettings');
    }

    function _show($this, data) {
        var settings = $this.data('saleInterfaceSettings');

        $this.append(
            $('<div>', {'class': 'saleScreen'}).append(
                wrapper = $('<div>', {'class': 'row wrapper'}).append(
                    $('<div>', {'class': 'span7 customer'}).append(
                        $('<div>', {'class': 'row title'}).html(settings.tCurrentCustomer),
                        $('<div>', {'class': 'row customerName'}).append(
                            $('<span>', {'class': 'name'}).html(data.person.name),
                            ' ',
                            $('<span>', {'class': 'university_identification'}).html('(' + data.person.universityIdentification + ')')
                        ),
                        $('<div>', {'class': 'row actions'}).append(
                            $('<button>', {'class': 'btn btn-info'}).append(
                                $('<i>', {'class': 'icon-comment icon-white'}),
                                settings.tComment
                            ),
                            $('<button>', {'class': 'btn btn-primary', 'data-key': 119}).append(
                                $('<i>', {'class': 'icon-eye-open icon-white'}),
                                settings.tQueue
                            ),
                            $('<button>', {'class': 'btn btn-success', 'data-key': 120}).append(
                                $('<i>', {'class': 'icon-ok-circle icon-white'}),
                                settings.tConclude
                            ),
                            $('<button>', {'class': 'btn btn-danger', 'data-key': 121}).append(
                                $('<i>', {'class': 'icon-remove icon-white'}),
                                settings.tCancel
                            )
                        )
                    )
                ),
                $('<table>', {'class': 'table table-striped table-bordered'}).append(
                    $('<thead>').append(
                        $('<tr>').append(
                            $('<th>', {'class': 'barcode'}).html(settings.tBarcode),
                            $('<th>', {'class': 'title'}).html(settings.tTitle),
                            $('<th>', {'class': 'status'}).html(settings.tStatus),
                            $('<th>', {'class': 'number'}).html(settings.tNumber),
                            $('<th>', {'class': 'price'}).html(settings.tPrice),
                            $('<th>', {'class': 'actions'}).html(settings.tActions)
                        )
                    ),
                    $('<tbody>', {'class': 'articles'})
                )
            )
        );

        if (settings.isSell) {
            wrapper.append(
                $('<div>', {'class': 'span2 discounts'}).append(
                    'Discounts:',
                    options = $('<div>', {'class': 'options'}).append(
                    )
                )
            );

            $(settings.discounts).each(function () {
                options.append(
                    $('<p>').append(
                        $('<label>', {'class': 'radio'}).append(
                            $('<input>', {'type': 'radio', 'name': 'discounts', 'value': this.type}),
                            ' ' + this.name
                        )
                    )
                )
            });

            wrapper.append(
                $('<div>', {'class': 'span3 money'}).append(
                    $('<div>', {'class': 'total'}).append(
                        '&euro; 0.00'
                    )
                )
            );
        }
    }
})(jQuery);