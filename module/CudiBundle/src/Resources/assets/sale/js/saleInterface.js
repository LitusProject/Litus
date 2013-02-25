(function ($) {
    var defaults = {
        isSell: true,
        discounts: [],
        membershipArticle: 0,

        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Finish',
        tCancel: 'Cancel',
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
        tAddArticle: 'Add Article',
        tBarcode: 'Barcode',
        tErrorTitle: 'Error',
        tErrorExtraArticle: 'An error occurred while adding the article.',

        saveComment: function (id, comment) {},
        showQueue: function () {},
        conclude: function (id, articles) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status},
        addArticle: function (id, barcode) {},
    };

    var firstAction = true;
    var queue = null;

    var methods = {
        init : function () {
            return this;
        },
        show : function (options, data) {
            $(this).data('saleInterfaceSettings', $.extend(defaults, options));

            $(this).data('data', data);
            _show($(this), data);
            return this;
        },
        hide : function () {
            $(this).html('');
            $(this).removeData('data');
            return this;
        },
        gotBarcode : function (barcode) {
            _gotBarcode($(this), barcode);
            return this;
        },
        addArticle : function (data) {
            _addExtraArticle($(this), data);
            return this;
        },
        getTotalPrice : function () {
            return _updatePrice($(this));
        },
        getSelectedDiscounts : function () {
            var discounts = [];
            var $this = $(this);
            $($(this).data('saleInterfaceSettings').discounts).each(function () {
                if ($this.find('.discounts input[value="' + this.type + '"]:checked').length > 0)
                    discounts.push(this.type);
            });
            return discounts;
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

    function _show($this, data) {
        var settings = $this.data('saleInterfaceSettings');

        $this.append(
            $('<div>', {'class': 'saleScreen'}).append(
                wrapper = $('<div>', {'class': 'row wrapper'}).append(
                    customer = $('<div>', {'class': 'span7 customer'}).append(
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
                                settings.tQueue + ' - F8'
                            ),
                            conclude = $('<button>', {'class': 'btn btn-success', 'data-key': 120}).append(
                                $('<i>', {'class': 'icon-ok-circle icon-white'}),
                                settings.tConclude + ' - F9'
                            ),
                            cancel = $('<button>', {'class': 'btn btn-danger', 'data-key': 121}).append(
                                $('<i>', {'class': 'icon-remove icon-white'}),
                                settings.tCancel + ' - F10'
                            )
                        )
                    ),
                    $('<div>', {'class': 'span12'}).append(
                        addArticle = $('<button>', {'class': 'btn btn-info pull-right', 'data-key': 118}).append(
                            $('<i>', {'class': 'icon-plus-sign icon-white'}),
                            settings.tAddArticle + ' - F7'
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
            customer.after(
                $('<div>', {'class': 'span2 discounts'}).append(
                    'Discounts:',
                    options = $('<div>', {'class': 'options'})
                ),
                $('<div>', {'class': 'span3 money'}).append(
                    $('<div>', {'class': 'total'}).append(
                        '&euro; 0.00'
                    )
                )
            );

            $(settings.discounts).each(function () {
                var checked = ('member' == this.type && data.person.member) || ('acco' == this.type && data.person.acco);
                var disabled = ('member' == this.type && !data.person.member);

                options.append(
                    $('<p>').append(
                        $('<label>', {'class': 'checkbox'}).append(
                            $('<input>', {'type': 'checkbox', 'name': 'discounts', 'value': this.type}).prop('checked', checked).prop('disabled', disabled).change(function () {
                                _updatePrice($this);
                            }),
                            ' ' + this.name
                        )
                    )
                )
            });
        }

        addArticle.click(function () {
            _addArticleModal($this);
        });

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

        _updatePrice($this);
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
            tbody.append(_addArticleRow($this, settings, this));
        });
    }

    function _addArticleRow($this, settings, data) {
        data.currentNumber = data.collected;

        var bestPrice = data.price;
        $(data.discounts).each(function () {
            if ($this.find('.discounts input[value="' + this.type + '"]:checked').length > 0)
                bestPrice = this.value < bestPrice ? this.value : bestPrice;
        });

        row = $('<tr>', {'class': 'article', 'id': 'article-' + data.articleId}).append(
            $('<td>').append(data.barcode),
            $('<td>').append(data.title),
            $('<td>').append(settings.translateStatus(data.status)),
            $('<td>').append(
                $('<span>', {class: 'currentNumber'}).html(data.collected),
                '/' + data.number
            ),
            $('<td class="price">').append('&euro; ' + (bestPrice/100).toFixed(2)),
            actions = $('<td>', {class: 'actions'})
        ).data('info', data);

        if ("booked" == data.status || data.sellable == false) {
            row.addClass('inactive');
        } else {
            actions.append(
                $('<button>', {class: 'btn btn-success addArticle'}).html(settings.tAdd).click(function () {
                    _addArticle($this, $(this).closest('tr').data('info').articleId);
                }).hide(),
                $('<button>', {class: 'btn btn-danger removeArticle'}).html(settings.tRemove).click(function () {
                    _removeArticle($this, $(this).closest('tr').data('info').articleId);
                }).hide()
            );
            _updateRow($this, row);
        }

        return row;
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
        var settings = $this.data('saleInterfaceSettings');
        var row = $this.find('#article-' + id);

        if (row.data('info').currentNumber < row.data('info').number) {
            row.data('info').currentNumber++;
            _updateRow($this, row)
            row.addClass('success').removeClass('error');
        } else {
            row.addClass('error').removeClass('success');
        }

        if (id == settings.membershipArticle)
            $this.find('.discounts input[value="member"]').prop('disabled', false).prop('checked', true);

        if (settings.isSell)
            _updatePrice($this);
    }

    function _removeArticle($this, id) {
        var settings = $this.data('saleInterfaceSettings');
        var row = $this.find('#article-' + id);

        if (row.data('info').currentNumber > 0) {
            row.data('info').currentNumber--;
            _updateRow($this, row)
            row.removeClass('error success');
        } else {
            row.addClass('error').removeClass('success');
        }

        if (id == settings.membershipArticle)
            $this.find('.discounts input[value="member"]').prop('disabled', true).prop('checked', false);

        if (settings.isSell)
            _updatePrice($this);
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
            if (articles[$(this).data('info').articleId] == undefined)
                articles[$(this).data('info').articleId] = 0;
            articles[$(this).data('info').articleId] += $(this).data('info').currentNumber;
        });
        settings.conclude($this.data('data').id, articles);
    }

    function _gotBarcode($this, barcode) {
        $this.find('tbody tr:not(.inactive)').each(function () {
            if ($(this).data('info').barcode == barcode) {
                $(this).find('.addArticle').click();
                return false;
            }
        });
    }

    function _addArticleModal($this) {
        var settings = $this.data('saleInterfaceSettings');

        $('body').append(
            modal = $('<div>', {'class': 'modal fade'}).append(
                $('<div>', {'class': 'modal-header'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {
                        $(this).closest('.modal').modal('hide').closest('.modal').on('hidden', function () {
                            $(this).remove();
                        });
                    }),
                    $('<h3>').html(settings.tAddArticle)
                ),
                $('<div>', {'class': 'modal-body'}).append(
                    $('<div>', {'class': 'form-horizontal'}).append(
                        $('<div>', {'class': 'control-group'}).append(
                            $('<label>', {'class': 'control-label', 'for': 'articleBarcode'}).html(settings.tBarcode),
                            $('<div>', {'class': 'controls'}).append(
                                $('<input>', {'type': 'text', 'id': 'articleBarcode', 'placeholder': settings.tBarcode})
                            )
                        )
                    )
                ),
                $('<div>', {'class': 'modal-footer'}).append(
                    $('<button>', {'class': 'btn btn-primary'}).html(settings.tAdd).click(function () {
                        settings.addArticle($this.data('data').id, $(this).closest('.modal').find('input').val());
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
        modal.find('input').focus();
    }

    function _addExtraArticle($this, data) {
        var settings = $this.data('saleInterfaceSettings');

        if (data.error) {
            $this.find('.saleScreen .flashmessage').remove();
            $this.find('.saleScreen').prepend(
                $('<div>', {'class': 'flashmessage alert alert-error fade'}).append(
                    $('<div>', {'class': 'title'}).html(settings.tErrorTitle),
                    $('<div>', {'class': 'content'}).append('<p>').html(settings.tErrorExtraArticle)
                ).addClass('in')
            );
        } else {
            if ($this.find('#article-' + data.articleId).length > 0) {
                row = $this.find('#article-' + data.articleId);
                data = row.data('info');
                if (data.status == 'assigned') {
                    data.number++;
                    row.find('td:nth-child(4)').html('').append(
                        $('<span>', {class: 'currentNumber'}).html(data.collected),
                        '/' + data.number
                    );
                } else {
                    data.status = 'assigned';
                    row.removeClass('inactive');
                    row.find('td:nth-child(6)').append(
                        $('<button>', {class: 'btn btn-success addArticle'}).html(settings.tAdd).click(function () {
                            _addArticle($this, $(this).closest('tr').data('info').articleId);
                        }).hide(),
                        $('<button>', {class: 'btn btn-danger removeArticle'}).html(settings.tRemove).click(function () {
                            _removeArticle($this, $(this).closest('tr').data('info').articleId);
                        }).hide()
                    );
                    _updateRow($this, row);
                }
                row.data('info', data);
            } else {
                $this.find('tbody').prepend(_addArticleRow($this, settings, data));
            }
        }
    }

    function _updatePrice($this) {
        var total = 0;
        $this.find('tbody tr:not(.inactive)').each(function () {
            var number = $(this).data('info').currentNumber;
            var appliedOnce = false;
            $(this).find('.price').html('');

            if (number == 0) {
                var bestPrice = parseInt($(this).data('info').price, 10);
                $($(this).data('info').discounts).each(function () {
                    if ($this.find('.discounts input[value="' + this.type + '"]:checked').length > 0)
                        bestPrice = this.value < bestPrice ? this.value : bestPrice;
                });
                $(this).find('.price').append(
                    $('<div>').html('&euro; ' + (bestPrice / 100).toFixed(2))
                );
            }

            while(number > 0) {
                var bestPrice = parseInt($(this).data('info').price, 10);
                var discount = null;
                $($(this).data('info').discounts).each(function () {
                    if ($this.find('.discounts input[value="' + this.type + '"]:checked').length > 0) {
                        if ((this.applyOnce && !appliedOnce) || !this.applyOnce) {
                            bestPrice = this.value < bestPrice ? this.value : bestPrice;
                            discount = this;
                            appliedOnce = true;
                        }
                    }
                });

                if (discount != undefined && discount.applyOnce) {
                    $(this).find('.price').append(
                        $('<div>').html('&euro; ' + (bestPrice / 100).toFixed(2) + ' (1x)')
                    );
                    total += bestPrice;
                    number -= 1;
                } else {
                    $(this).find('.price').append(
                        $('<div>').html('&euro; ' + (bestPrice / 100).toFixed(2) + ' (' + number + 'x)')
                    );
                    total += number * bestPrice;
                    number = 0;
                }
            }
        });

        $this.find('.money .total').html('&euro; ' + (total / 100).toFixed(2));

        return total;
    }
})(jQuery);