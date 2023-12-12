const pug = require('pug');

module.exports = function (path, callback) {
	try {
		const deps = pug.compileFile(path).dependencies;
		callback(null, {
			path,
			deps
		});
	} catch (err) {
		console.log('ERROR =>>>>', err);
		callback(err, null);
	}

}