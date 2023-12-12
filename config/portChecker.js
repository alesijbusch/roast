var net = require('net');

module.exports = function (port, host) {
	let nextPort = port;
	return new Promise((resolve, reject) => {
		const checkPort = () => {
			var server = net.createServer(function (socket) {
				socket.write('Echo server\r\n');
				socket.pipe(socket);
			});

			server.listen(nextPort, host);
			server.on('error', function (e) {
				nextPort += 2;
				checkPort();
			});
			server.on('listening', function (e) {
				server.close();
				resolve(nextPort)
			});
		}
		checkPort();
	})
};

// getEmptyPort(8888, '127.0.0.1').then((port) => {
// 	console.log('port', port);
// })