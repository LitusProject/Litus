(function ($) {
	$.autoHideFlashMessages = function (options) {
		var defaults = {
			timeOut	 : 5000,
			speed	 : 400,
		};
		
		options = $.extend(defaults, options);
		
		setTimeout(function () {
			$('.flashmessage').each(function () {
				if ($(this).hasClass('fade'))
					$(this).removeClass('in');
				else
					$('.flashmessage').slideUp(options.speed);
			});
		}, options.timeOut);
	}
}) (jQuery)