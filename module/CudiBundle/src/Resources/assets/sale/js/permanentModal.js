(function ($) {
	$.fn.permanentModal = function(method, options) {
		options = $.extend({}, options, {closable: false});
	
		if ('open' == method) {
			$(this).modal();
			
			$(this).on('shown', function () {
				if (options.closable) {
					$(this).find('.close').show();
				} else {
					$(this).find('.close').hide();
					$('.modal-backdrop').unbind('click');
					$(document).off('keyup.dismiss.modal');
				}
			});
		} else if ('hide' == method) {
			$(this).modal('hide');
		}
	}
}) (jQuery);