(function ($) {
	$.searchDatabase = function (opts) {
		var defaults = {
			defaultPage: null,
			searchDiv: null,
			searchString: null,
			searchField: null,
			url: '',
			minLength: 3,
			display: function () {}
		};

		opts = $.extend(defaults, opts);

		$(document).keydown(function (e) {
			if (70 == e.keyCode && (e.metaKey || e.ctrlKey)) {
				e.preventDefault();
				if (opts.searchDiv.is(':visible')) {
					opts.searchDiv.hide();
					opts.defaultPage.show();
				} else {
					opts.searchDiv.show();
					opts.defaultPage.hide();
					opts.searchString.focus();
				}
			}
		}).keyup(function (e) {
			if (27 == e.keyCode) {
			    e.preventDefault();
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
					} else if (opts.searchString.val().length < opts.minLength) {
						return;
					}
					$.ajax({
						url: stripTrailingSlash(opts.url) + '/' + opts.searchField.val() + '/' + opts.searchString.val(),
						method: 'get',
						dataType: 'json',
						success: opts.display
					});
				}, 200));
			});
		});

		function stripTrailingSlash(str) {
			if(str.substr(-1) == '/')
			    return str.substr(0, str.length - 1);
			return str;
		}
	};
}) (jQuery);