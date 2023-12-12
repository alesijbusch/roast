const pug = require('pug');

module.exports = function (page, callback) {
	try {
		const render = pug.renderFile(`./src/pages/${page}/${page}.pug`, { pretty: true });
		callback(null, render);
	} catch (err) {
		console.log('ERROR =>>>>', err);
		callback(err, null);
	}

}