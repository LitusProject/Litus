(function ($) {
    $.fn.downloadFile = function (file) {
		$(this).click(function () {
			$('iframe.downloadFile').remove();
			var iframe = $('<iframe>', {width: 1, height: 1, frameborder: 0, class: 'export', css: {display: 'none'}}).appendTo('body');
			setTimeout(function () {
				var body = (iframe.prop('contentDocument') !== undefined) ? iframe.prop('contentDocument').body : iframe.prop('document').body;
				body = $(body);
				body.html('<form action="'+file+'" method="post"></form>');
				body.find('form').submit();
			    }, 50);
			return false;
	    });
    }
})(jQuery);