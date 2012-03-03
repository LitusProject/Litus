(function ($) {
	$.fn.autoHideFlashMessages = function (options) {
		var defaults = {
			timeOut	 : 5000,
			speed	 : 400,
		};
		options = $.extend(defaults, options);
		
		$(this).each(function () {
			var $this = $(this);
			clearTimeout($this.data('timer'));
			
			var timer = setTimeout(function () {
				if ($this.hasClass('fade'))
					$this.removeClass('in');
				else
					$this.slideUp(options.speed);
			}, options.timeOut);
			
			$this.data('timer', timer);
		});
	}
}) (jQuery);