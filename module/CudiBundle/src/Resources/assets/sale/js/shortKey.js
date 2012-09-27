(function ($) {
	$('body').bind('keydown.shortKey', function (e) {
		$('[data-key]:visible:not([data-dismiss="key"])').each(function () {
            if ($(this).offset().top < 0)
                return;
			if (e.which == $(this).data('key')) {
				e.preventDefault();
				$(this).click();
				return false;
			}
		});
	});
}) (jQuery);