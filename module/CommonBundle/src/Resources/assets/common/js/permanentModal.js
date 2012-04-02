(function ($) {
    var defaults = {
        closable: false
    };
    
    var methods = {
        open: function (options) {
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
            
            return this;
        },
        hide: function () {
			$(this).modal('hide');
			
			return this;
        }
    };
    
    $.fn.permanentModal = function (method) {
    	if (methods[method]) {
    		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    	} else if (typeof method === 'object' || ! method) {
    		return methods.open.apply(this, arguments);
    	} else {
    		$.error('Method ' +  method + ' does not exist on $.formUploadProgress');
    	}
    };
}) (jQuery);