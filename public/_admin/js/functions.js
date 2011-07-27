/**
 * This file is a part of the Eleutheria framework, which aims to make the life of webdevelopers easier.
 *
 * 
 */

// Remap jQuery to $
(function($){})(window.jQuery);

// Trigger when the page is ready.
$(document).ready(function (){

	$('.submenu .subtitle p').click(function(event) {
		var target = event.target;
		if(event.target.nodeName == 'B')
			var target = $(event.target).parent();
		$('#submenu_' + $(target).attr('id').substring(6, $(target).attr('id').length).toLowerCase()).toggle();
	});

});


/* optional triggers

$(window).load(function() {
	
});

$(window).resize(function() {
	
});

*/