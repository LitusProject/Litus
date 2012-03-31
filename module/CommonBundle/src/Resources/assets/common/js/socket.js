(function ($) {
	var defaults = {
		name: 'webSocket',
		url: '',
		open: function(){},
		error: function(){},
		message: function(){}
	};
	
	var methods = {
		init : function (options) {
			var settings = $.extend(defaults, options);
			
			var ws = new WebSocket(options.url);
			
			$(ws)
				.bind('open', settings.open)
				.bind('close', function (e) {
					setTimeout(function () {$.webSocket(settings)}, 1000);
					settings.error(e);
				})
				.bind('error', function (e) {
					setTimeout(function () {$.webSocket(settings)}, 1000);
					settings.error(e);
				})
				.bind('message', function (e) {
					if (e.type == 'message' && e.originalEvent.data)
						settings.message(e.originalEvent, $.parseJSON(e.originalEvent.data));
				});
						
			$(window).unload(function(){
				$.webSocket('close', settings);
			});
			
			$(document).data(settings.name, ws);
			return this;
		},
		send : function (data) {
			var socket = $(document).data(data.name);
			if (socket != undefined)
				socket.send(data.text);
			return this;
		},
		close : function (data) {
			$(document).data(data.name).close();
			$(document).removeData(data.name);
			return this;
		}
	};
	
	$.extend({
		webSocket: function ( method ) {
			if ( methods[ method ] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || ! method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on $.webSocket' );
			}
		}
	});
}) (jQuery);