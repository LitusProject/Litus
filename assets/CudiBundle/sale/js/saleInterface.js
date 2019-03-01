(function ($) {
    var defaults = {
        isSell: true,
        discounts: [],
        articleTypeahead: '',
        membershipArticles: [{'id': 0, 'barcode': 0}],
        lightVersion: false,
        barcodeLength: 12,

        tCurrentCustomer: 'Current Customer',
        tDiscounts: 'Discounts',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Finish',
        tCancel: 'Cancel',
        tArticle: 'Article',
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
        tPrintNext: 'Print Next',
        tErrorExtraArticle: 'An error occurred while adding the article.',

        saveComment: function (id, comment) {},
        showQueue: function () {},
        conclude: function (id, articles) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status;},
        addArticle: function (id, articleId) {},
        printNextInQueue: function () {},
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
                if ($this.find('.discounts input[value="' + this.type + '"]').is(':checked')) {
                    discounts.push(this.type);
                }
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
                $('<div>', {'class': 'row mb-4'}).append(
                    customer = $('<div>', {'class': 'col-7'}).append(
                        $('<div>', {'class': 'd-flex flex-column h-100'}).append(
                            $('<div>').html(settings.tCurrentCustomer),
                            $('<div>', {'class': 'ml-4 my-auto'}).append(
                                $('<div>', {'class': 'd-flex'}).append(
                                    $('<div>', {'class': 'align-self-end'}).append(
                                        $('<h3>', {'class': 'mb-0'}).html(data.person.name)
                                    ),
                                    $('<div>', {'class': 'align-self-end ml-1'}).html('(' + data.person.universityIdentification + ')')
                                )
                            )
                        )
                    )
                ),
                $('<div>', {'class': 'row justify-content-center mb-4'}).append(
                    $('<div>', {'class': 'col-10 text-center'}).append(
                        showQueue = $('<button>', {'class': 'btn btn-primary mr-1', 'data-key': 119}).append(
                            $('<i>', {'class': 'fas fa-list-ol mr-1'}),
                            settings.tQueue + ' - F8'
                        ),
                        printNextInQueue = $('<button>', {'class': 'btn btn-primary mr-1', 'data-key': 118}).append(
                            $('<i>', {'class': 'fas fa-print mr-1'}),
                            settings.tPrintNext + ' - F7'
                        ),
                        addArticle = $('<button>', {'class': 'btn btn-secondary pull-right mr-1', 'data-key': 116}).append(
                            $('<i>', {'class': 'fas fa-plus-circle mr-1'}),
                            settings.tAddArticle + ' - F5'
                        ),
                        editComments = $('<button>', {'class': 'btn btn-secondary mr-1'}).append(
                            $('<i>', {'class': 'fas fa-comments mr-1'}),
                            settings.tComments
                        ),
                        cancel = $('<button>', {'class': 'btn btn-danger mr-1', 'data-key': 121}).append(
                            $('<i>', {'class': 'fas fa-minus-circle mr-1'}),
                            settings.tCancel + ' - F10'
                        ),
                        conclude = $('<button>', {'class': 'btn btn-success', 'data-key': 120}).append(
                            $('<i>', {'class': 'fas fa-check-circle mr-1'}),
                            settings.tConclude + ' - F9'
                        )
                    )
                ),
                $('<div>', {'class': 'row'}).append(
                    $('<table>', {'class': 'table table-striped'}).append(
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
            )
        );

        if (settings.lightVersion) {
            showQueue.hide();
        }

        if (settings.isSell) {
            customer.after(
                $('<div>', {'class': 'col-2 discounts'}).append(
                    $('<div>', {'class': 'd-flex flex-column'}).append(
                        $('<div>').html(settings.tDiscounts),
                        options = $('<div>', {'class': 'ml-2 mt-1'})
                    )
                ),
                $('<div>', {'class': 'col-3 text-center my-auto'}).append(
                    $('<h1>', {'class': 'display-4 mb-0 money'}).html('&euro; 0.00')
                )
            );

            $(settings.discounts).each(function () {
                var checked = (this.type == 'member' && data.person.member) || (this.type == 'acco' && data.person.acco);

                options.append(
                    $('<div>', {'class': 'custom-control custom-checkbox mb-1'}).append(
                        $('<input>', {'class': 'custom-control-input', 'type': 'checkbox', 'name': 'discounts', 'value': this.type, 'id': 'discount' + this.name})
                            .prop('checked', checked)
                            .change(function () {
                                _updatePrice($this);
                            }),
                        $('<label>', {'class': 'custom-control-label', 'for': 'discount' + this.name}).html(this.name)
                    )
                );
            });
        }

        addArticle.click(function () {
            _addArticleModal($this);
        });

        editComments.click(function () {
            _editComments($this);
        });

        printNextInQueue.click(function () {
            settings.printNextInQueue();
        });

        showQueue.click(function () {
            settings.showQueue();
        });

        cancel.click(function () {
            settings.cancel(data.id);
        });

        conclude.click(function () {
            _conclude($this);
        });

        _addArticles($this, data.articles);

        $(document).keydown(function (e) {
            _keyControls($this, e);
        });

        _updatePrice($this);
    }

    function _editComments($this) {
        var settings = $this.data('saleInterfaceSettings');

        $('body').append(
            modal = $('<div>', {'class': 'modal fade'}).append(
                $('<div>', {'class': 'modal-dialog modal-lg'}).append(
                    $('<div>', {'class': 'modal-content'}).append(
                        $('<div>', {'class': 'modal-header'}).append(
                            $('<h5>', {'class': 'modal-title'}).html(settings.tComments),
                            $('<button>', {'type': 'button', 'class': 'close', 'data-dismiss': 'modal'}).append(
                                $('<span>').html('&times;')
                            )
                        ),
                        $('<div>', {'class': 'modal-body'}).append(
                            $('<div>', {'class': 'input-group'}).append(
                                $('<textarea>', {'class': 'form-control', 'rows': 10}).val($this.data('data').comment)
                            )
                        ),
                        $('<div>', {'class': 'modal-footer'}).append(
                            $('<button>', {'class': 'btn btn-secondary', 'data-dismiss': 'modal'}).html(settings.tClose),
                            $('<button>', {'class': 'btn btn-primary'}).html(settings.tSave).click(function () {
                                $this.data('data').comment = $(this).closest('.modal').find('textarea').val();
                                settings.saveComment($this.data('data').id, $this.data('data').comment);

                                modal.modal('toggle');
                            })
                        )
                    )
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
            if ($this.find('.discounts input[value="' + this.type + '"]').is(':checked')) {
                bestPrice = this.value < bestPrice ? this.value : bestPrice;
            }
        });

        row = $('<tr>', {'class': 'article', 'id': 'article-' + data.articleId}).append(
            $('<td>').append(data.barcode),
            $('<td>').append(data.title),
            $('<td>').append(settings.translateStatus(data.status)),
            $('<td>').append(
                $('<span>', {'class': 'currentNumber'}).html(data.collected),
                '/' + data.number
            ),
            $('<td class="price">').append('&euro; ' + (bestPrice/100).toFixed(2)),
            actions = $('<td>', {'class': 'actions'})
        ).data('info', data);

        if (data.status == 'booked' || data.sellable == false) {
            row.addClass('inactive');
        } else {
            actions.append(
                $('<button>', {'class': 'btn btn-success mr-1 addArticle'}).html(settings.tAdd).click(function () {
                    _addArticle($this, $(this).closest('tr').data('info').articleId);
                }).hide(),
                $('<button>', {'class': 'btn btn-danger removeArticle'}).html(settings.tRemove).click(function () {
                    _removeArticle($this, $(this).closest('tr').data('info').articleId);
                }).hide()
            );

            _updateRow($this, row);
        }

        return row;
    }

    function _keyControls($this, e) {
        var activeRow = $this.find('tbody tr.article.activeRow:first');

        if (e.which == 40) { // arrow up
            e.preventDefault();

            if (activeRow.length == 0) {
                $this.find('tr.article:not(.inactive):first').addClass('activeRow');
            } else {
                activeRow.removeClass('activeRow');
                _updateRow($this, activeRow);

                activeRow.next('.article:not(.inactive)').addClass('activeRow');
            }

            activeRow = $this.find('tbody tr.article.activeRow:first');
            if (activeRow.length > 0) {
                _updateRow($this, activeRow);
            }
        } else if (e.which == 38) { // arrow down
            e.preventDefault();

            if (activeRow.length == 0) {
                $this.find('tr.article:not(.inactive):last').addClass('activeRow');
            } else {
                activeRow.removeClass('activeRow');
                _updateRow($this, activeRow);

                activeRow.prev('.article:not(.inactive)').addClass('activeRow');
            }

            activeRow = $this.find('tbody tr.article.activeRow:first');
            if (activeRow.length > 0) {
                _updateRow($this, activeRow);
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
        $this.find('#article-' + id + ':not(.inactive)').each(function () {
            if ($(this).data('info').currentNumber < $(this).data('info').number) {
                $(this).data('info').currentNumber++;
                _updateRow($this, $(this));
                return false;
            }
        });

        if (_isMemberShipArticleId(id, settings.membershipArticles)) {
            $this.find('.discounts input[value="member"]').prop('checked', true);
        }

        if (settings.isSell) {
            _updatePrice($this);
        }
    }

    function _removeArticle($this, id) {
        var settings = $this.data('saleInterfaceSettings');
        $this.find('#article-' + id + ':not(.inactive)').each(function () {
            if ($(this).data('info').currentNumber > 0) {
                $(this).data('info').currentNumber--;
                _updateRow($this, $(this));
            }
        });

        if (_isMemberShipArticleId(id, settings.membershipArticles)) {
            $this.find('.discounts input[value="member"]').prop('checked', false);
        }

        if (settings.isSell) {
            _updatePrice($this);
        }
    }

    function _updateRow($this, row) {
        var data = row.data('info');
        row.find('.currentNumber').html(data.currentNumber);

        row.find('.removeArticle').toggle(data.currentNumber > 0);
        row.find('.addArticle').toggle(data.currentNumber < data.number);

        row.removeClass('table-primary');
        if (row.hasClass('activeRow')) {
            row.addClass('table-primary');
        }

        if (data.currentNumber == data.number) {
            row.addClass('text-success').removeClass('text-danger');
        } else if (data.currentNumber > 0 ) {
            row.addClass('text-danger').removeClass('text-success');
        } else {
            row.removeClass('text-danger text-success');
        }
    }

    function _conclude($this) {
        var settings = $this.data('saleInterfaceSettings');

        var articles = {};
        $this.find('tbody tr:not(.inactive)').each(function () {
            if (articles[$(this).data('info').articleId] == undefined) {
                articles[$(this).data('info').articleId] = 0;
            }

            articles[$(this).data('info').articleId] += $(this).data('info').currentNumber;
        });

        settings.conclude($this.data('data').id, articles);
    }

    function _barcodeEquals(length, one, two) {
        one = ['' + one];
        two = ['' + two];

        if (one[0].length < length) {
            one[0] = '0'.repeat(length - one[0].length) + one[0];
            one[1] = '0' + one[0];
        }

        if (two[0].length < length) {
            two[0] = '0'.repeat(length - two[0].length) + two[0];
            two[1] = '0' + two[0];
        }

        var found = false;

        return one.some(function (o) {
            return two.some(function (t) {
                return o.substring(0, length) === t.substring(0, length);
            });
        });
    }

    function _gotBarcode($this, barcode) {
        var settings = $this.data('saleInterfaceSettings');

        var found = false;
        $this.find('tbody tr:not(.inactive)').each(function () {
            var $this = $(this);
            var length = settings.barcodeLength;

            if (_barcodeEquals(length, $this.data('info').barcode, barcode)) {
                $this.find('.addArticle').click();
                found = true;
            }

            $($this.data('info').barcodes).each(function () {
                if (_barcodeEquals(length, this, barcode)) {
                    $this.find('.addArticle').click();
                    found = true;
                    return false;
                }
            });

            if (found) {
                return false;
            }
        });

        if (found) {
            return;
        }

        $(settings.membershipArticles).each(function () {
            if (this.barcode == barcode) {
                $this.find('tbody').prepend(
                    _addArticleRow($this, settings, {
                        articleId: this.id,
                        barcode: this.barcode,
                        title: this.title,
                        price: this.price,
                        collected: 0,
                        number: 1,
                        status: 'assigned',
                        sellable: true,
                    })
                );

                _addArticle($this, this.id);
                return false;
            }
        });
    }

    function _addArticleModal($this) {
        var settings = $this.data('saleInterfaceSettings');

        $('body').append(
            modal = $('<div>', {'class': 'modal fade'}).append(
                $('<div>', {'class': 'modal-dialog modal-lg'}).append(
                    $('<div>', {'class': 'modal-content'}).append(
                        $('<div>', {'class': 'modal-header'}).append(
                            $('<h5>', {'class': 'modal-title'}).html(settings.tAddArticle),
                            $('<button>', {'type': 'button', 'class': 'close', 'data-dismiss': 'modal'}).append(
                                $('<span>').html('&times;')
                            )
                        ),
                        $('<div>', {'class': 'modal-body'}).append(
                            $('<div>').append(
                                articleId = $('<input>', {'type': 'hidden', 'id': 'articleAddTypeaheadId'}),
                                $('<div>', {'class': 'input-group'}).append(
                                    article = $('<input>', {'type': 'text', 'class': 'form-control', 'id': 'articleAddTypeahead', 'placeholder': settings.tArticle})
                                )
                            )
                        ),
                        $('<div>', {'class': 'modal-footer'}).append(
                            $('<button>', {'class': 'btn btn-secondary', 'data-dismiss': 'modal'}).html(settings.tClose),
                            addButton = $('<button>', {'class': 'btn btn-primary disabled', 'data-key': 13}).html(settings.tAdd)
                        )
                    )
                )
            )
        );

        article.typeaheadRemote({
            source: settings.articleTypeahead,
        }).change(function (e) {
            if ($(this).data('value')) {
                articleId.val($(this).data('value').id);

                addButton.removeClass('disabled').unbind().click(function () {
                    settings.addArticle($this.data('data').id, articleId.val());
                    modal.modal('toggle');
                });
            }
        });

        modal.on('shown.bs.modal', function () {
            $(this).find('input').focus();
        });
        modal.modal();
    }

    function _addExtraArticle($this, data) {
        var settings = $this.data('saleInterfaceSettings');

        if (data.error) {
            $this.find('.saleScreen .alert').alert('close');
            $this.find('.saleScreen').prepend(
                $('<div>', {'class': 'alert alert-danger fade show'}).html(settings.tErrorExtraArticle)
            );
        } else {
            $this.find('.saleScreen .alert').alert('close');

            if ($this.find('#article-' + data.articleId).length > 0) {
                row = $this.find('#article-' + data.articleId);
                data = row.data('info');
                if (data.status == 'assigned') {
                    data.number++;
                    data.currentNumber++;
                    row.find('td:nth-child(4)').html('').append(
                        $('<span>', {'class': 'currentNumber'}).html(data.currentNumber),
                        '/' + data.number
                    );
                    _updatePrice($this);
                } else {
                    data.status = 'assigned';
                    row.removeClass('inactive');
                    row.find('td:nth-child(6)').append(
                        $('<button>', {'class': 'btn btn-success addArticle'}).html(settings.tAdd).click(function () {
                            _addArticle($this, $(this).closest('tr').data('info').articleId);
                        }).hide(),
                        $('<button>', {'class': 'btn btn-danger removeArticle'}).html(settings.tRemove).click(function () {
                            _removeArticle($this, $(this).closest('tr').data('info').articleId);
                        }).hide()
                    );
                    _updateRow($this, row);
                }
                row.data('info', data);
            } else {
                $this.find('tbody').prepend(_addArticleRow($this, settings, data));
                _addArticle($this, data.articleId);
            }
        }
    }

    function _updatePrice($this) {
        var total = 0;
        $this.find('tbody tr:not(.inactive)').each(function () {
            var number = $(this).data('info').currentNumber;
            var appliedOnce = false;
            var bestPrice = 0;
            $(this).find('.price').html('');

            if (number == 0) {
                bestPrice = parseInt($(this).data('info').price, 10);
                $($(this).data('info').discounts).each(function () {
                    if ($this.find('.discounts input[value="' + this.type + '"]').is(':checked')) {
                        bestPrice = this.value < bestPrice ? this.value : bestPrice;
                    }
                });

                $(this).find('.price').append(
                    $('<div>').html('&euro; ' + (bestPrice / 100).toFixed(2))
                );
            }

            while(number > 0) {
                bestPrice = parseInt($(this).data('info').price, 10);
                var discount = null;
                $($(this).data('info').discounts).each(function () {
                    if ($this.find('.discounts input[value="' + this.type + '"]').is(':checked')) {
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

        $this.find('.money').html('&euro; ' + (total / 100).toFixed(2));

        return total;
    }

    function _isMemberShipArticleId(id, membershipArticles) {
        var found = false;
        $(membershipArticles).each(function () {
            if (this.id == id) {
                found = true;
                return false;
            }
        });
        return found;
    }
})(jQuery);
