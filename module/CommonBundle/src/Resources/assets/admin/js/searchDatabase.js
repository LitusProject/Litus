(function ($) {
	$.searchDatabase = function (opts) {
		$(document).keypress(function (e) {
			if (102 == e.keyCode && e.metaKey) {
				if (opts.searchDiv.is(':visible')) {
					opts.searchDiv.hide();
					opts.defaultPage.show();
				} else {
					opts.searchDiv.show();
					opts.defaultPage.hide();
					opts.searchString.focus();
				}
				return false;
			}
		}).keyup(function (e) {
			if (27 == e.keyCode) {
				opts.searchDiv.hide();
				opts.defaultPage.show();
			}
		});
		
		opts.searchString.data('timeout', null);
		$.each([opts.searchField, opts.searchString], function (i, v) {
			v.bind('change click keyup', function () {
				clearTimeout(opts.searchString.data('timeout'));
				opts.searchString.data('timeout', setTimeout(function () {
					if ('' == opts.searchString.val()) {
						opts.clear();
						return;
					}
					$.ajax({
						url: opts.url + '/field/' + opts.searchField.val() + '/string/' + opts.searchString.val(),
						method: 'get',
						dataType: 'json',
						success: opts.display
					});
				}, 100));
			});
		});
	};
}) (jQuery);