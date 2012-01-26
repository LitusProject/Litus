$(document).ready(function (){

	$('.submenu .subtitle p').click(function(event) {
		var target = event.target;
		if(event.target.nodeName == 'B')
			var target = $(event.target).parent();
		$('#submenu_' + $(target).attr('id').substring(6, $(target).attr('id').length).toLowerCase()).toggle();
	});

});