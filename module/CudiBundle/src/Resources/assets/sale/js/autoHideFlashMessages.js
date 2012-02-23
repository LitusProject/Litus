(function ($) {
	$.autoHideFlashMessages = function (options) {
		var defaults = {
			timeOut	 : 5000,
			speed	 : 400,
		};
		
		options = $.extend(defaults, options);
		
		setTimeout(function () {
			$('.flashmessage').slideUp(options.speed);
		}, options.timeOut);
	}
}) (jQuery)