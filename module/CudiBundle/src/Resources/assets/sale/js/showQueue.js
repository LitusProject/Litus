(function ($) {
    var defaults = {
        url: '',
        session: 0,
        errorDialog: null,
        barcodePrefix: 0,
        statusTranslate: function () {},
        openSale: function () {},
        closeSale: function () {},
        isSelling: function () {},
    };

    var methods = {
        gotBarcode: function (value) {
            _gotBarcode($(this), value);
            return this;
        },
    	init: function (options) {
    	    var settings = $.extend(defaults, options);

    	    $(this).data('showQueueSettings', settings);

    	    _init($(this));

    	    return this;
        },
        setPayDesk: function (payDesk) {
            _setPayDesk($(this), payDesk);

            return this;
        },
        updateActions: function () {
            _updateActions($(this));
        },
        updatePayDesk: function () {
            _setPayDesk($(this), $(this).data('payDesk'));

            return this;
        },
    }

    $.fn.showQueue = function (method) {
    	if (methods[method]) {
    		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    	} else if (typeof method === 'object' || ! method) {
    		return methods.init.apply(this, arguments);
    	} else {
    		$.error('Method ' +  method + ' does not exist on $.showQueue');
    	}
    };

    function _addActions ($this, row) {
        var options = $this.data('showQueueSettings');

        _visibilityActions(row);

        startCollecting.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: startCollecting ' + $(this).parent().data('servingQueueId'));
    	});
        cancelCollecting.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: cancelCollecting ' + $(this).parent().data('servingQueueId'));
    	});
        stopCollecting.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: stopCollecting ' + $(this).parent().data('servingQueueId'));
    	});
        hold.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: setHold ' + $(this).parent().data('servingQueueId'));
    	});
        unhold.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: unsetHold ' + $(this).parent().data('servingQueueId'));
    	});
        startSelling.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: startSelling ' + $(this).parent().data('servingQueueId'));
    	});
        cancelSelling.unbind('click')
            .filter(':not(.disabled)').click(function () {
                $this.showQueue('updatePayDesk');
        		_sendToSocket('action: cancelSelling ' + $(this).parent().data('servingQueueId'));
    	});
    }

    function _gotBarcode ($this, value) {
        var options = $this.data('showQueueSettings');
        value = value - options.barcodePrefix;

        $this.find('tr').each(function () {
            if ($(this).data('info').id == value) {
                switch ($(this).data('info').status) {
                    case 'collecting':
                        $(this).find('.stopCollecting').click();
                        break;
                    case 'collected':
                        $(this).find('.startSelling').click();
                        break;
                }
            }
        });
    }

    function _init ($this) {
        var options = $this.data('showQueueSettings');
        $('#hideHold').click(function () {
            _toggleHoldItems($this);
        });
        $.webSocket(
        	{
        		name: 'showQueue',
        		url: options.url,
        		open: function (e) {
        			options.errorDialog.removeClass('in');
					$.webSocket('send', {name: 'showQueue', text: 'initialize: {"queueType": "shortQueue", "session": "' + options.session + '" }'});
					if ($this.data('payDesk'))
					    _setPayDesk($this, $this.data('payDesk'));
                },
                message: function (e, data) {
                    options.errorDialog.removeClass('in');
                    if (data.queue) {
                        var inQueue = [];
                        $(data.queue).each(function () {
                            inQueue.push(parseInt(this.id, 10));
                            if ($this.find('#queueItem-' + this.id).length > 0)
                                _updateQueueItem($this, this);
                            else
                                _showQueueItem($this, this);
                        });

                        $this.find('tr').each(function () {
                            if ($.inArray(parseInt($(this).data('info').id, 10), inQueue) < 0)
                                $(this).remove();
                        });

                        _toggleHoldItems($this);
        			} else if(data.sale) {
                        $this.showQueue('updatePayDesk');
        				options.openSale('showQueue', data);
        			} else if(data.collecting && options.collectScanning) {
                        $this.showQueue('updatePayDesk');
                        options.openCollecting('showQueue', data);
                    } else if (data.error) {
                        if (data.error == 'paydesk')
                            $this.showQueue('updatePayDesk');
                    }
        		},
        		error: function (e) {
        			options.errorDialog.addClass('in');
        			$this.html('');
        			options.closeSale();
        		}
        	}
        );
    }

    function _sendToSocket (text) {
    	$.webSocket('send', {name: 'showQueue', text: text});
    }

    function _setPayDesk ($this, payDesk) {
        _sendToSocket('action: setPayDesk ' + payDesk);
        $this.data('payDesk', payDesk);
    }

    function _showQueueItem ($this, item) {
        var options = $this.data('showQueueSettings');

		var row = $('<tr>', {'id': 'queueItem-' + item.id}).append(
			$('<td>', {'class': 'number'}).html(item.number),
			$('<td>', {'class': 'name'}).append(
			    (item.name ? item.name : 'guest ' + item.id),
			    ' ',
			    (item.payDesk ? $('<span>', {class: 'label label-info'}).html(item.payDesk) : '')
			),
			$('<td>', {'class': 'status'}).html(options.statusTranslate(item.status)),
			actions = $('<td>', {'class': 'actions'})
		).data('info', item);

		actions.data('servingQueueId', item.id);

        actions.append(
            startCollecting = $('<button>', {'class': 'btn btn-success startCollecting'}).html('Print').hide(),
            stopCollecting = $('<button>', {'class': 'btn btn-success stopCollecting'}).html('Done').hide(),
            cancelCollecting = $('<button>', {'class': 'btn btn-danger cancelCollecting'}).html('Cancel').hide(),
            startSelling = $('<button>', {'class': 'btn btn-success startSelling'}).html('Sell').hide(),
            cancelSelling = $('<button>', {'class': 'btn btn-danger cancelSelling'}).html('Cancel').hide(),
            hold = $('<button>', {'class': 'btn btn-warning setHold hold'}).html('Hold').hide(),
            unhold = $('<button>', {'class': 'btn btn-warning unsetHold'}).html('Unhold').hide()
        );

		$this.append(row);
        _addActions($this, row);
	}

    function _updateQueueItem($this, item) {
        var options = $this.data('showQueueSettings');
        var row = $('#queueItem-' + item.id);
        var previousStatus = row.data('info').status;

        row.find('.number').html(item.number);
        row.find('.name').html('').append(
            (item.name ? item.name : 'guest ' + item.id),
            ' ',
            (item.payDesk ? $('<span>', {class: 'label label-info'}).html(item.payDesk) : '')
        );
        row.find('.status').html(options.statusTranslate(item.status));
        row.data('info', item);

        if (previousStatus != item.status)
            _addActions($this, row);

        if (options.isSelling()) {
            row.find('.startCollecting, .startSelling').hide();
        } else {
            if (item.status == 'signed_in')
                row.find('.startCollecting').show();
            else if (item.status == 'collected')
                row.find('.startSelling').show();
        }
    }

    function _toggleHoldItems($this) {
        if ($('#hideHold').is(':checked')) {
            $this.find('tr').each(function () {
                if ($(this).data('info').status == 'hold')
                    $(this).hide();
            });
        } else {
            $this.find('tr').show();
        }
    }

    function _visibilityActions(row) {
        row.find('button').hide();
        var startCollecting = row.find('.startCollecting');
        var stopCollecting = row.find('.stopCollecting');
        var stopCollecting = row.find('.stopCollecting');
        var startSelling = row.find('.startSelling');
        var cancelSelling = row.find('.cancelSelling');
        var hold = row.find('.setHold');
        var unhold = row.find('.unsetHold');

        switch (row.data('info').status) {
            case 'signed_in':
                startCollecting.show();
                hold.show();
                break;
            case 'collecting':
                stopCollecting.show();
                cancelCollecting.show();
                hold.show();
                break;
            case 'collected':
                startSelling.show();
                hold.show();
                break;
            case 'selling':
                cancelSelling.show();
                hold.show();
                break;
            case 'hold':
                unhold.show();
                break;
        }
        if (row.data('info').locked)
            row.find('button').addClass('disabled');
        else
            row.find('button').removeClass('disabled');
    }

    function _updateActions($this) {
        var options = $this.data('showQueueSettings');

        $this.find('tr').each(function() {
            _visibilityActions($(this));
        });
    }
}) (jQuery);