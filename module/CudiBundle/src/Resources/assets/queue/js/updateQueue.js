;(function ($) {
	$.updateQueue = function (options) {
		createSocket();
	
		function createSocket() {
			try {
				var socket = new WebSocket(options.url);
				var nbTries = 0;
			
				socket.onopen = function (msg) {
					socket.send('queueUpdated');
					nbTries = 0;
				};
				
				socket.onclose = recreateSocket;
			} catch (ex) {
				recreateSocket();
			};	
		};
		
		function recreateSocket() {
			options.error();
			setTimeout(createSocket, Math.min(nbTries*200, 5000));
			nbTries++;
		}
	};
}) (jQuery);