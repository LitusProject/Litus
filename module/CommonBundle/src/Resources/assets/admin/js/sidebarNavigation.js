(function ($) {
	$.fn.sidebarNavigation = function() {
		return $(this).find('.subtitle').each(function () {
				$(this).click(function () {
						$(this).parent().find('ul').toggle();
					});
			});
	};
}) (jQuery)