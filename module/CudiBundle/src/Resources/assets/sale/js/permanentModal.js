(function ($) {
	$.fn.permanentModal = function(method, options) {
		options = $.extend({closable: false}, options);

		var $this = $(this);
		
		if ('open' == method) {
			$(this).on('shown', function () {
				if (options.closable == true) {
					$(this).find('.close').show();
					$('.modal-backdrop').unbind('click').click(function () {
						$this.modal('hide');
					});
					$(document).on('keyup.dismiss.modal', function ( e ) {
					  e.which == 27 && $this.modal('hide')
					})
				} else {
					$(this).find('.close').hide();
					$('.modal-backdrop').unbind('click');
					$(document).off('keyup.dismiss.modal');
				}
			});
			
			$(this).modal('show');
		} else if ('hide' == method) {
			$(this).modal('hide');
		}
		
		return this;
	}
}) (jQuery);