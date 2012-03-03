;(function ($) {
	$.loadQueue = function (options) {
		var nbTries = 0;
		
		createSocket();
	
		function createSocket() {
			try {
				var socket = new WebSocket(options.url);
			
				socket.onopen = function (msg) {
					nbTries = 0;
					socket.send('queue-type: ' + options.type);
				};
				socket.onmessage = function (e) {
					if (!e.data)
						return;
					
					options.loaded($.parseJSON(e.data));
				};
				socket.onclose = recreateSocket;
				socket.onerror = function () {
					console.log('error');
					options.error();
					recreateSocket();
				}
			} catch (ex) {
				recreateSocket();
			};	
		};
		
		function recreateSocket() {
			console.log('closed');
			options.error();
			setTimeout(createSocket, Math.min(nbTries*200, 5000));
			nbTries++;
		}
	};
}) (jQuery);