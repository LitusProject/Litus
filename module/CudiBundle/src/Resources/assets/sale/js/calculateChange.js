(function ($) {
	$('body').data('calculateChange', {
		timer: null,
		buffer: 0
	});
	
	$('body').bind('keydown.calculateChange', function (e) {
		if (e.which >= 48 && e.which <= 57) {
			setBuffer(getBuffer() * 10 + (e.which - 48));
			$('#payedMoney').html((getBuffer() / 100).toFixed(2)).data('value', getBuffer());
			calculateChange();
			$('#totalMoney').change(calculateChange);
			
			clearTimeout($('body').data('calculateChange').timer);
			var timer = setTimeout(clearBuffer, 5000);
			$('body').data('calculateChange').timer = timer;
		} else if (e.which == 67) {
			clearBuffer();
			$('#payedMoney').html((getBuffer() / 100).toFixed(2)).data('value', getBuffer());
			calculateChange();
		}
		
		function clearBuffer () {
			setBuffer(0);
		}
		
		function getBuffer () {
			var buffer = $('body').data('calculateChange');
			return buffer ? buffer.buffer : 0;
		}
		
		function setBuffer (value) {
			$('body').data('calculateChange').buffer = value;
		}
		
		function calculateChange () {
			$('#payedMoney').data('value') == 0 ?
				$('#changeMoney').html((0).toFixed(2)):
				$('#changeMoney').html((($('#payedMoney').data('value') - $('#totalMoney').data('value')) / 100 ).toFixed(2));
		}
	});
}) (jQuery);