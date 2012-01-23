/**
 * Litus
 *
 * Copyright 2011 Litus
 * Licensed under the
 *
 * Author: Pieter Maene <pieter.maene@litus.cc>
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