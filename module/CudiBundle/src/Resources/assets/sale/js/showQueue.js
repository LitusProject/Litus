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
    
    function _addActions ($this) {
    	$this.find('.startCollecting:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: startCollecting ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.cancelCollecting:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: cancelCollecting ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.stopCollecting:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: stopCollecting ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.setHold:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: setHold ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.unsetHold:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: unsetHold ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.startSelling:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: startSelling ' + $(this).parent().data('servingQueueId'));
    	});
    	$this.find('.cancelSelling:not(.disabled)').unbind('click').click(function () {
    		_sendToSocket('action: cancelSelling ' + $(this).parent().data('servingQueueId'));
    	});
    }
    
    function _gotBarcode ($this, value) {
        var options = $this.data('showQueueSettings');
             
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
        				$this.html('');
        				$(data.queue).each(function () {
        					_showQueueItem($this, this);
        				});
        				_addActions($this);
        			} else if(data.sale) {
        				options.openSale('showQueue', data);
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
        console.log('setpaydesk');
        _sendToSocket('action: setPayDesk ' + payDesk);
        $this.data('payDesk', payDesk);
    }
    
    function _showQueueItem ($this, item) {
        var options = $this.data('showQueueSettings');

		var row = $('<tr>').append(
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

		switch (item.status) {
			case 'signed_in':
				actions.append(
					$('<button>', {'class': 'btn btn-success startCollecting'}).html('Print'),
					' ',
					$('<button>', {'class': 'btn btn-warning setHold hold'}).html('Hold')
				);
				break;
			case 'collecting':
				actions.append(
					$('<button>', {'class': 'btn btn-success stopCollecting'}).html('Done'),
					' ',
					$('<button>', {'class': 'btn btn-danger cancelCollecting'}).html('Cancel'),
					' ',
					$('<button>', {'class': 'btn btn-warning setHold hold'}).html('Hold')
				);
				break;
			case 'collected':
				actions.append(
					$('<button>', {'class': 'btn btn-success startSelling'}).html('Sell'),
					' ',
					$('<button>', {'class': 'btn btn-warning setHold hold'}).html('Hold')
				);
				break;
			case 'selling':
				actions.append(
					$('<button>', {'class': 'btn btn-danger cancelSelling'}).html('Cancel'),
					' ',
					$('<button>', {'class': 'btn btn-warning setHold hold'}).html('Hold')
				);
				break;
			case 'hold':
				actions.append(
					$('<button>', {'class': 'btn btn-warning unsetHold'}).html('Unhold')
				);
				break;
		}
		if (item.locked)
			row.find('button').addClass('disabled');
			
		if (options.isSelling())
		    actions.find('.startSelling').hide();
		
		$this.append(row);
	}
}) (jQuery);