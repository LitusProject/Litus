(function ($) {
    var defaults = {
        isSell: true,
        discounts: [],

        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue - F8',
        tConclude: 'Finish - F9',
        tCancel: 'Cancel - F10',
        tBarcode: 'Barcode',
        tTitle: 'Title',
        tStatus: 'Status',
        tNumber: 'Number',
        tPrice: 'Price',
        tActions: 'Actions',
        tClose: 'Close',
        tSave: 'Save',
        tAdd: 'Add',
        tRemove: 'Remove',

        saveComment: function (id, comment) {},
        showQueue: function () {},
        conclude: function (id, articles) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status},
    };

    var firstAction = true;
    var queue = null;

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            $(this).data('saleInterfaceSettings', settings);

            return this;
        },
        show : function (data) {
            $(this).data('data', data);
            _show($(this), data);
            return this;
        },
        hide : function () {
            $(this).html('');
            $(this).removeData('data');
            return this;
        },
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
                            editComment = $('<button>', {'class': 'btn btn-info'}).append(
                                $('<i>', {'class': 'icon-comment icon-white'}),
                                settings.tComments
                            ),
                            showQueue = $('<button>', {'class': 'btn btn-primary', 'data-key': 119}).append(
                                $('<i>', {'class': 'icon-eye-open icon-white'}),
                                settings.tQueue
                            ),
                            conclude = $('<button>', {'class': 'btn btn-success', 'data-key': 120}).append(
                                $('<i>', {'class': 'icon-ok-circle icon-white'}),
                                settings.tConclude
                            ),
                            cancel = $('<button>', {'class': 'btn btn-danger', 'data-key': 121}).append(
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

        editComment.click(function () {
            _editComment($this);
        });

        showQueue.click(function () {
            settings.showQueue();
        });

        cancel.click(function () {
            settings.cancel(data.id);
        });

        conclude.click(function () {
            _conclude($this);
        })

        _addArticles($this, data.articles);

        $(document).bind('keydown.sale', function  (e) {
            _keyControls($this, e);
        });
    }

    function _editComment($this) {
        var settings = $this.data('saleInterfaceSettings');

        $('body').append(
            modal = $('<div>', {'class': 'modal fade'}).append(
                $('<div>', {'class': 'modal-header'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {
                        $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
                            $(this).remove();
                        });
                    }),
                    $('<h3>').html(settings.tComments)
                ),
                $('<div>', {'class': 'modal-body'}).append(
                    $('<textarea>', {'style': 'width: 97%', 'rows': 10}).val($this.data('data').comment)
                ),
                $('<div>', {'class': 'modal-footer'}).append(
                    $('<button>', {'class': 'btn btn-primary'}).html(settings.tSave).click(function () {
                        $this.data('data').comment = $(this).closest('.modal').find('textarea').val();
                        settings.saveComment($this.data('data').id, $this.data('data').comment);
                        $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
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
    }

    function _addArticles($this, articles) {
        var settings = $this.data('saleInterfaceSettings');
        var tbody = $this.find('tbody');

        $(articles).each(function () {
            this.currentNumber = this.collected;
            tbody.append(
                row = $('<tr>', {'class': 'article', 'id': 'article-' + this.id}).append(
                    $('<td>').append(this.barcode),
                    $('<td>').append(this.title),
                    $('<td>').append(settings.translateStatus(this.status)),
                    $('<td>').append(
                        $('<span>', {class: 'currentNumber'}).html(this.collected),
                        '/' + this.number
                    ),
                    $('<td class="price">').append('&euro;' + (0).toFixed(2)),
                    actions = $('<td>', {class: 'actions'})
                ).data('info', this)
            );

            if ("booked" == this.status) {
                row.addClass('inactive');
            } else {
                actions.append(
                    $('<button>', {class: 'btn btn-success addArticle'}).html(settings.tAdd).click(function () {
                        _addArticle($this, $(this).closest('tr').data('info').id);
                    }).hide(),
                    $('<button>', {class: 'btn btn-danger removeArticle'}).html(settings.tRemove).click(function () {
                        _removeArticle($this, $(this).closest('tr').data('info').id);
                    }).hide()
                );
                _updateRow($this, row);
            }
        });
    }

    function _keyControls($this, e) {
        var activeRow = $this.find('tbody tr.article.info:first');

        if (e.which == 40) { // arrow up
            e.preventDefault();

            if (activeRow.length == 0) {
                $this.find('tr.article:not(.inactive):first').addClass('info');
            } else {
                activeRow.removeClass('info');
                activeRow.next('.article:not(.inactive)').addClass('info');
            }
        } else if (e.which == 38) { // arrow down
            e.preventDefault();

            if (activeRow.length == 0) {
                $this.find('tr.article:not(.inactive):last').addClass('info');
            } else {
                activeRow.removeClass('info');
                activeRow.prev('.article:not(.inactive)').addClass('info');
            }
        } else if (e.which == 187) { // plus
            e.preventDefault();

            activeRow.find('.addArticle').click();
        } else if (e.which == 189) { // minus
            e.preventDefault();

            activeRow.find('.removeArticle').click();
        }
    }

    function _addArticle($this, id) {
        var row = $this.find('#article-' + id);

        if (row.data('info').currentNumber < row.data('info').number) {
            row.data('info').currentNumber++;
            _updateRow($this, row)
            row.addClass('success').removeClass('error');
        } else {
            row.addClass('error').removeClass('success');
        }
    }

    function _removeArticle($this, id) {
        var row = $this.find('#article-' + id);

        if (row.data('info').currentNumber > 0) {
            row.data('info').currentNumber--;
            _updateRow($this, row)
            row.removeClass('error success');
        } else {
            row.addClass('error').removeClass('success');
        }
    }

    function _updateRow($this, row) {
        var data = row.data('info');
        row.find('.currentNumber').html(data.currentNumber);

        row.find('.removeArticle').toggle(data.currentNumber > 0);
        row.find('.addArticle').toggle(data.currentNumber < data.number);
    }

    function _conclude($this) {
        var settings = $this.data('saleInterfaceSettings');

        var articles = {};
        $this.find('tbody tr:not(.inactive)').each(function () {
            articles[$(this).data('info').articleId] = $(this).data('info').currentNumber;
        });
        settings.conclude($this.data('data').id, articles);
    }
})(jQuery);