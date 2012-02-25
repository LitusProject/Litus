;(function ($) {
	$.loadQueue = function (options) {
		var nbTries = 0;
		
		createSocket();
	
		function createSocket() {
			try {
				if ('WebSocket' in window) {
					socket = new WebSocket(options.url);
				} else if ('MozWebSocket' in window) {
					socket = new MozWebSocket(options.url);
				} else {
					options.error();
					return;
				}
			
				socket.onopen = function (msg) {
					socket.send('queueUpdated');
					nbTries = 0;
				};
				socket.onmessage = function (e) {
					if (!e.data)
						return;
					
					options.loaded($.parseJSON(e.data));
				};
				socket.onclose = recreateSocket;
				socket.onerror = options.error;
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
}) (jQuery)