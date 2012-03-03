(function ($) {
	$.fn.showQueue = function (options) {
		var $this = $(this);
		
		$.loadQueue({
			url    : options.url,
			type   : 'shortQueue',
			loaded : function (data) {
				options.errorDialog.removeClass('in');
				$this.html('');
				$(data).each(function () {
					showQueueItem(this);
				});
			},
			error  : function () {
				options.errorDialog.addClass('in');
				$this.html('');
			}
		});
		
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
						$('<button>', {'class': 'btn btn-success'}).html('Print'),
						' ',
						$('<button>', {'class': 'btn btn-warning'}).html('Hold')
					);
					break;
				case 'collecting':
					actions.append(
						$('<button>', {'class': 'btn btn-success'}).html('Done'),
						' ',
						$('<button>', {'class': 'btn btn-danger'}).html('Cancel')
					);
					break;
				case 'collected':
					actions.append(
						$('<button>', {'class': 'btn btn-success'}).html('Sell')
					);
					break;
				case 'selling':
					actions.append(
						$('<button>', {'class': 'btn btn-danger'}).html('Cancel')
					);
					break;
				case 'hold':
					actions.append(
						$('<button>', {'class': 'btn btn-warning'}).html('Unhold')
					);
					break;
			}
			$this.append(row);
		}
	}
}) (jQuery);