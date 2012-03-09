(function ($) {
	$.fn.showQueue = function (options) {
		var $this = $(this);
		
		$.webSocket(
			{
				name: 'showQueue',
				url: options.url,
				open: function (e) {
					$.webSocket('send', {name: 'showQueue', text: 'queue-type: shortQueue'});
				},
				message: function (e, data) {
					options.errorDialog.removeClass('in');
					if (data.queue) {
						$this.html('');
						$(data.queue).each(function () {
							showQueueItem(this);
						});
						addActions();
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
		
		function showQueueItem(item) {
			var row = $('<tr>').append(
				$('<td>', {'class': 'number'}).html(item.number),
				$('<td>', {'class': 'name'}).html(item.name ? item.name : 'guest ' + item.id),
				$('<td>', {'class': 'status'}).html(options.statusTranslate(item.status)),
				actions = $('<td>', {'class': 'actions'})
			);
			
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
			
			$this.append(row);
		}
		
		function sendToSocket(text) {
			$.webSocket('send', {name: 'showQueue', text: text});
		}
		
		function addActions() {
			$this.find('.startCollecting:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: startCollecting ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.cancelCollecting:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: cancelCollecting ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.stopCollecting:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: stopCollecting ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.setHold:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: setHold ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.unsetHold:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: unsetHold ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.startSelling:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: startSelling ' + $(this).parent().data('servingQueueId'));
			});
			$this.find('.cancelSelling:not(.disabled)').unbind('click').click(function () {
				sendToSocket('action: cancelSelling ' + $(this).parent().data('servingQueueId'));
			});
		}
	}
}) (jQuery);