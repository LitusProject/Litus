;(function ($) {
	$.loadQueue = function (options) {
		load();
		
		function load() {
			$.post(options.url, function (data) {
				options.loaded(data);
				setTimeout(load, options.interval);
			}, 'json').error(options.error);
		}
	};
}) (jQuery)