;(function ($) {
	$.loadQueue = function (options) {
		load();
		
		function load() {
			$.post(options.url, function (data) {
				options.loaded(data);
				setTimeout(load, options.interval);
			}, 'json').error(function () {
				options.error()
				setTimeout(load, options.interval)
			});
		}
	};
}) (jQuery)