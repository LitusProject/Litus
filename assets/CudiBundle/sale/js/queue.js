(function ($) {
    var defaults = {
        tQueueTitle: 'Queue',
        tQueueTitleLightVersion: 'Add Person',
        tUniversityIdentification: 'University Identification',
        tNumber: '#',
        tName: 'Name',
        tStatus: 'Status',
        tActions: 'Actions',
        tPrint: 'Print',
        tDone: 'Done',
        tCancel: 'Cancel',
        tScan: 'Scan',
        tSell: 'Sell',
        tHold: 'Hold',
        tUnhold: 'Unhold',
        tHideHold: 'Hide Hold',
        tUndoLastSale: 'Undo Last Sale',
        tPrintNext: 'Print Next',
        tSellNext: 'Sell Next',
        tNotFoundInQueue: '<i><b>{{ name }}</b> was not found in the queue.</i>',
        tAddToQueue: 'Add to Queue',
        tErrorAddPerson: 'The person could not be added to the queue.',
        tErrorAddPersonType: {
            'person': 'The person was not found.',
            'noBookings': 'There were no bookings for this person.'
        },
        tNoNextToPrint: 'There is no next item to print.',
        tNoNextToSell: 'There is no next item to sell.',

        translateStatus: function (status) {return status;},
        sendToSocket: function (text) {},
        lightVersion: false,
        personTypeahead: '',
    };

    var lastSold = 0;

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            $(this).data('queueSettings', settings);

            if (settings.lightVersion) {
                _initLightVersion($(this));
            } else {
                _init($(this));
            }
            return this;
        },
        show : function (options) {
            var permanent = (options == undefined || options.permanent == undefined) ? true : options.permanent;
            currentView = permanent ? 'queue' : currentView;
            $(this).permanentModal({closable: !permanent});

            if ($(this).data('queueSettings').lightVersion) {
                $(this).find('.filterText').val('');
                $(this).on('shown', function () {
                    $(this).find('.filterText').focus();
                });
            } else {
                var $this = $(this);
                $(this).find('tbody tr').each(function () {
                    _showActions($this, $(this), $(this).data('info'));
                });
            }
            return this;
        },
        hide : function (options) {
            $(this).permanentModal('hide');
            return this;
        },
        updateQueue : function (data) {
            _updateQueue($(this), data);
            return this;
        },
        updateQueueItem : function (data) {
            _updateQueueItem($(this), data);
            return this;
        },
        setLastSold : function (data) {
            lastSold = data;
            $(this).find('.undoLastSale').toggle(lastSold > 0);
            return this;
        },
        gotBarcode : function (barcode) {
            _gotBarcode($(this), barcode);
            return this;
        },
        addPersonError : function (error) {
            _addPersonError($(this), error);
            return this;
        },
        printNextInQueue : function () {
            _printNextInQueue($(this));
            return this;
        }
    };

    $.fn.queue = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.queue');
        }
    };

    $.queue = function (options) {
        return $('<div>').queue(options);
    };

    function _init($this) {
        var settings = $this.data('queueSettings');

        $this.addClass('modal fade queueModal').html('').append(
            $('<div>', {'class': 'modal-dialog modal-lg'}).append(
                $('<div>', {'class': 'modal-content'}).append(
                    $('<div>', {'class': 'modal-header'}).append(
                        $('<div>', {'class': 'container-fluid'}).append(
                            $('<div>', {'class': 'row'}).append(
                                $('<div>', {'class': 'col-8 my-auto'}).append(
                                    $('<h5>', {'class': 'modal-title'}).html(settings.tQueueTitle)
                                ),
                                $('<div>', {'class': 'col-4 pr-0'}).append(
                                    $('<div>', {'class': 'input-group'}).append(
                                        filterText = $('<input>', {'type': 'text', 'class': 'form-control filterText', 'placeholder': settings.tUniversityIdentification}),
                                        clearFilter = $('<div>', {'class': 'input-group-append'}).append(
                                            $('<span>', {'class': 'input-group-text', 'style': 'cursor: pointer;'}).append(
                                                $('<i>', {'class': 'fas fa-times'})
                                            )
                                        ),
                                    )
                                )
                            )
                        )
                    ),
                    $('<div>', {'class': 'modal-body'}).append(
                        $('<table>', {'class': 'table table-striped'}).append(
                            $('<thead>').append(
                                $('<tr>').append(
                                    $('<th>', {'class': 'number'}).html(settings.tNumber),
                                    $('<th>', {'class': 'name'}).html(settings.tName),
                                    $('<th>', {'class': 'status'}).html(settings.tStatus),
                                    $('<th>', {'class': 'actions'}).html(settings.tActions)
                                )
                            ),
                            $('<tbody>')
                        )
                    ),
                    $('<div>', {'class': 'modal-footer'}).append(
                        $('<div>', {'class': 'container-fluid'}).append(
                            $('<div>', {'class': 'row'}).append(
                                $('<div>', {'class': 'col-3 my-auto pl-0'}).append(
                                    $('<div>', {'class': 'custom-control custom-checkbox'}).append(
                                        hideHold = $('<input>', {'class': 'custom-control-input hideHold', 'type': 'checkbox', 'id': 'hideHold'}).prop('checked', true),
                                        $('<label>', {'class': 'custom-control-label', 'for': 'hideHold'}).html(settings.tHideHold)
                                    ),
                                ),
                                $('<div>', {'class': 'col-9 pr-0 text-right'}).append(
                                    undoLastSale = $('<button>', {'class': 'btn btn-danger mr-1 undoLastSale', 'data-key': 117}).append(
                                        $('<i>', {'class': 'fas fa-undo mr-1'}),
                                        settings.tUndoLastSale + ' - F6'
                                    ).hide(),
                                    sellNext = $('<button>', {'class': 'btn btn-success mr-1', 'data-key': 116}).append(
                                        $('<i>', {'class': 'fas fa-shopping-cart mr-1'}),
                                        settings.tSellNext + ' - F5'
                                    ),
                                    printNext = $('<button>', {'class': 'btn btn-success', 'data-key': 118}).append(
                                        $('<i>', {'class': 'fas fa-print mr-1'}),
                                        settings.tPrintNext + ' - F7'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        hideHold.change(function () {
            $this.find('tbody tr').each(function () {
                _toggleVisibility($this, $(this), $(this).data('info'));
            });
        });

        clearFilter.click(function () {
            filterText.val('');
            filterText.trigger('keyup');
        });

        filterText.keyup(function () {
            var filter = $(this).val().toLowerCase();
            var pattern = new RegExp(/[a-z][0-9]{7}/);

            if (pattern.test(filter)) {
                var found = false;
                $this.find('tbody tr').each(function () {
                    if ($(this).data('info') && $(this).data('info').university_identification.toLowerCase().indexOf(filter) == 0) {
                        found = true;
                    }
                });

                if (!found) {
                    if ($this.find('tbody #addToQueue').length == 0) {
                        $this.find('tbody').append(
                            $('<tr>', {'id': 'addToQueue'}).append(
                                $('<td>', {'class': 'number'}),
                                $('<td>', {'class': 'name'}).html(
                                    settings.tNotFoundInQueue.replace('{{ name }}', filter)
                                ),
                                $('<td>', {'class': 'status'}),
                                $('<td>', {'class': 'actions'}).append(
                                    $('<button>', {'class': 'btn btn-success'}).html(settings.tAddToQueue).data('id', filter).click(function () {
                                        settings.sendToSocket(
                                            JSON.stringify({
                                                'command': 'action',
                                                'action': 'addToQueue',
                                                'universityIdentification': filter,
                                            })
                                        );
                                    })
                                )
                            )
                        );
                    }
                } else {
                    $this.find('tbody #addToQueue').remove();
                }
            } else {
                $this.find('tbody #addToQueue').remove();
            }

            $this.find('tbody tr').each(function () {
                _toggleVisibility($this, $(this), $(this).data('info'));
            });
        });

        printNext.click(function () {
            _printNextInQueue($this);
        });

        sellNext.click(function () {
            _sellNextInQueue($this);
        });

        undoLastSale.click(function () {
            if (lastSold > 0) {
                settings.sendToSocket(
                    JSON.stringify({
                        'command': 'action',
                        'action': 'undoSale',
                        'id': lastSold,
                    })
                );
            }
            $(this).hide();
        });
    }

    function _initLightVersion($this) {
        var settings = $this.data('queueSettings');

        $this.addClass('modal fade').html('').append(
            $('<div>', {'class': 'modal-dialog modal-lg'}).append(
                $('<div>', {'class': 'modal-content'}).append(
                    $('<div>', {'class': 'modal-header'}).append(
                        $('<h5>', {'class': 'modal-title'}).html(settings.tQueueTitleLightVersion)
                    ),
                    $('<div>', {'class': 'modal-body'}).append(
                        $('<div>', {'class': 'input-group'}).append(
                            filterText = $('<input>', {'type': 'text', 'class': 'form-control filterText', 'placeholder': settings.tUniversityIdentification}),
                            clearFilter = $('<div>', {'class': 'input-group-append'}).append(
                                $('<span>', {'class': 'input-group-text', 'style': 'cursor: pointer;'}).append(
                                    $('<i>', {'class': 'fas fa-broom'})
                                )
                            ),
                        )
                    ),
                    $('<div>', {'class': 'modal-footer'}).append(
                        undoLastSale = $('<button>', {'class': 'btn btn-danger undoLastSale', 'data-key': 117}).append(
                            $('<i>', {'class': 'glyphicon glyphicon-arrow-left'}),
                            settings.tUndoLastSale + ' - F6'
                        ).hide(),
                        startSale = $('<button>', {'class': 'btn btn-success disabled startSale', 'data-key': 118}).append(
                            $('<i>', {'class': 'glyphicon glyphicon-print'}),
                            settings.tSell + ' - F7'
                        )
                    )
                )
            )
        );

        filterText.keyup(function (e) {
            $(this).removeData('value');
            var filter = $(this).val().toLowerCase();
            var pattern = new RegExp(/[a-z][0-9]{7}/);

            if (pattern.test(filter)) {
                $this.find('.startSale').removeClass('disabled').unbind('click').click(function () {
                    settings.sendToSocket(
                        JSON.stringify({
                            'command': 'action',
                            'action': 'addToQueue',
                            'universityIdentification': filter,
                        })
                    );
                });

                if (e.keyCode == 13) {
                    $this.find('.startSale').click();
                }
            } else {
                $this.find('.startSale').addClass('disabled').unbind('click');
            }
        }).change(function (e) {
            if ($(this).data('value')) {
                filterText.val($(this).data('value').universityIdentification);
                settings.sendToSocket(
                    JSON.stringify({
                        'command': 'action',
                        'action': 'addToQueue',
                        'universityIdentification': $(this).data('value').universityIdentification,
                    })
                );
            }
        }).typeaheadRemote(
            {
                'source': settings.personTypeahead,
            }
        );

        undoLastSale.click(function () {
            if (lastSold > 0) {
                settings.sendToSocket(
                    JSON.stringify({
                        'command': 'action',
                        'action': 'undoSale',
                        'id': lastSold,
                    })
                );
            }
            $(this).hide();
        });
    }

    function _updateQueue($this, data) {
        var settings = $this.data('queueSettings');
        var tbody = $this.find('tbody');
        var inQueue = [];

        var currentList = new Object();
        tbody.find('tr').each(function () {
            currentList[$(this).attr('id')] = $(this);
        });

        $(data).each(function () {
            inQueue.push(this.id);

            var item = currentList['item-' + this.id];
            if (undefined == item) {
                item = _createItem($this, settings, this);
                tbody.append(item);
            } else {
                _updateItem($this, settings, item, this);
            }

            _toggleVisibility($this, item, this);
        });

        tbody.find('tr').each(function () {
            var pos = $.inArray($(this).data('info').id, inQueue);
            if (pos < 0) {
                $(this).remove();
            } else {
                inQueue.splice(pos, 1);
            }
        });
    }

    function _updateQueueItem($this, data) {
        var settings = $this.data('queueSettings');
        if (settings.lightVersion) {
            if (data.university_identification.toLowerCase() == $this.find('.filterText').val().toLowerCase()) {
                $this.find('.filterText').val('');
                $this.find('.startSale').addClass('disabled').unbind('click');
                settings.sendToSocket(
                    JSON.stringify({
                        'command': 'action',
                        'action': 'startSale',
                        'id': data.id,
                    })
                );
            }
        } else {
            var item = $this.find('tbody #item-' + data.id);

            if (data.status == 'sold') {
                item.remove();
                return;
            }

            if (item.length == 0) {
                item = _createItem($this, settings, data);
                $this.find('tbody').append(item);
            } else {
                _updateItem($this, settings, item, data);
            }

            _toggleVisibility($this, item, data);
        }
    }

    function _showActions($this, row, data) {
        switch (data.status) {
            case 'signed_in':
                if (currentView == 'collect') {
                    row.find('.hold').show();
                    row.find('.startCollecting, .startScanning, .stopCollecting, .cancelCollecting, .startSale, .cancelSale, .unhold').hide();
                } else {
                    row.find('.startCollecting, .hold').show();
                    row.find('.stopCollecting, .startScanning, .cancelCollecting, .startSale, .cancelSale, .unhold').hide();
                }

                break;
            case 'collecting':
                if (data.displayScanButton) {
                    row.find('.startScanning, .cancelCollecting, .hold').show();
                    row.find('.startCollecting, .stopCollecting, .startSale, .cancelSale, .unhold').hide();
                } else {
                    row.find('.stopCollecting, .cancelCollecting, .hold').show();
                    row.find('.startCollecting, .startScanning, .startSale, .cancelSale, .unhold').hide();
                }

                break;
            case 'collected':
                if (currentView == 'collect') {
                    row.find('.hold').show();
                    row.find('.startCollecting, .startScanning, .stopCollecting, .cancelCollecting, .startSale, .cancelSale, .unhold').hide();
                } else {
                    row.find('.startSale, .hold').show();
                    row.find('.startCollecting, .startScanning, .stopCollecting, .cancelCollecting, .cancelSale, .unhold').hide();
                }

                break;
            case 'selling':
                row.find('.cancelSale, .hold').show();
                row.find('.startCollecting, .startScanning, .stopCollecting, .cancelCollecting, .startSale, .unhold').hide();

                break;
            case 'hold':
                row.find('.unhold').show();
                row.find('.startCollecting, .startScanning, .stopCollecting, .cancelCollecting, .startSale, .cancelSale, .hold').hide();

                break;
        }

        if (data.locked) {
            row.find('button').prop('disabled', true);
        } else {
            row.find('button').prop('disabled', false);
        }
    }

    function _updateItem($this, settings, row, data) {
        var previousStatus = '';
        if (row.data('info')) {
            previousStatus = row.data('info').status;
        }

        row.find('.number').html(data.number);
        row.find('.name').html('').append(
            data.name,
            ' ',
            (data.payDesk ? $('<span>', {'class': 'badge badge-primary'}).html(data.payDesk) : '')
        );
        row.find('.status').html(settings.translateStatus(data.status));
        row.data('info', data);

        if (previousStatus != data.status) {
            _showActions($this, row, data);
        }
    }

    function _createItem($this, settings, data) {
        var row = $('<tr>', {'id': 'item-' + data.id}).append(
            $('<th>', {'class': 'number', 'scope': 'row'}),
            $('<td>', {'class': 'name'}),
            $('<td>', {'class': 'status'}),
            $('<td>', {'class': 'actions'}).append(
                startCollecting = $('<button>', {'class': 'btn btn-success mr-1 startCollecting'}).html(settings.tPrint).hide(),
                stopCollecting = $('<button>', {'class': 'btn btn-success mr-1 stopCollecting'}).html(settings.tDone).hide(),
                startScanning = $('<button>', {'class': 'btn btn-success mr-1 startScanning'}).html(settings.tScan).hide(),
                cancelCollecting = $('<button>', {'class': 'btn btn-danger mr-1 cancelCollecting'}).html(settings.tCancel).hide(),
                startSale = $('<button>', {'class': 'btn btn-success mr-1 startSale'}).html(settings.tSell).hide(),
                cancelSale = $('<button>', {'class': 'btn btn-danger mr-1 cancelSale'}).html(settings.tCancel).hide(),
                hold = $('<button>', {'class': 'btn btn-warning float-right mr-1 hold'}).html(settings.tHold).hide(),
                unhold = $('<button>', {'class': 'btn btn-warning unhold'}).html(settings.tUnhold).hide()
            )
        );

        _updateItem($this, settings, row, data);

        startCollecting.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'startCollecting',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        startScanning.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'startCollecting',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        stopCollecting.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'stopCollecting',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        cancelCollecting.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'cancelCollecting',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        startSale.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'startSale',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        cancelSale.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'cancelSale',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        hold.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'hold',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        unhold.click(function () {
            if ($(this).is('.disabled')) {
                return;
            }

            settings.sendToSocket(
                JSON.stringify({
                    'command': 'action',
                    'action': 'unhold',
                    'id': $(this).closest('tr').data('info').id,
                })
            );
        });

        return row;
    }

    function _toggleVisibility($this, row, data) {
        if (data == undefined) {
            row.show();
            return;
        }

        var show = true;
        if ($this.find('.hideHold').is(':checked') && data.status == 'hold') {
            show = false;
        }

        var filter = $this.find('.filterText').val();
        if (filter.length > 0) {
            filter = filter.toLowerCase();

            show = false;
            if (data.name.toLowerCase().indexOf(filter) >= 0 || data.university_identification.toLowerCase().indexOf(filter) >= 0) {
                show = true;
            }
        }

        if (show) {
            $this.find('tbody #addToQueue').remove();
        }

        row.toggle(show);
    }

    function _gotBarcode($this, barcode) {
        var settings = $this.data('queueSettings');

        $this.find('tbody tr:visible').each(function () {
            if ($(this).data('info').barcode == barcode) {
                switch ($(this).data('info').status) {
                    case 'collecting':
                        if ($(this).find('.startScanning').is(':visible')) {
                            $(this).find('.startScanning').click();
                        } else {
                            $(this).find('.stopCollecting').click();
                        }

                        break;
                    case 'collected':
                        $(this).find('.startSale').click();
                        break;
                }
            }
        });
    }

    function _addPersonError($this, error) {
        var settings = $this.data('queueSettings');

        $this.find('.modal-body').prepend(
            $('<div>', {'class': 'alert alert-danger fade show'}).html(
                settings.tErrorAddPerson + (error === undefined ? '' : ': ' + settings.tErrorAddPersonType[error])
            )
        );

        setTimeout(function () {
            $this.find('.modal-body .alert').alert('close');
        }, 2000);
    }

    function _printNextInQueue($this) {
        var settings = $this.data('queueSettings');
        var nextPrinted = false;

        $this.find('tbody tr').each(function () {
            if ($(this).data('info').status == 'signed_in' && !$(this).data('info').collectPrinted) {
                nextPrinted = true;
                settings.sendToSocket(
                    JSON.stringify({
                        'command': 'action',
                        'action': 'startCollectingBulk',
                        'id': $(this).data('info').id,
                    })
                );
                return false;
            }
        });

        if (!nextPrinted) {
            $this.find('.modal-body').prepend(
                $('<div>', {'class': 'alert alert-danger fade show'}).html(settings.tNoNextToPrint)
            );

            setTimeout(function () {
                $this.find('.modal-body .alert').alert('close');
            }, 2000);
        }
    }

    function _sellNextInQueue($this) {
        var settings = $this.data('queueSettings');
        var nextSelling = false;

        $this.find('tbody tr').each(function () {
            if ($(this).data('info').status == 'collected' && $(this).find('.startSale').is(':visible')) {
                nextSelling = true;
                $(this).find('.startSale').click();
                return false;
            }
        });

        if (!nextSelling) {
            $this.find('.modal-body').prepend(
                $('<div>', {'class': 'alert alert-danger fade show'}).html(settings.tNoNextToSell)
            );

            setTimeout(function () {
                $this.find('.modal-body .alert').alert('close');
            }, 2000);
        }
    }
})(jQuery);
