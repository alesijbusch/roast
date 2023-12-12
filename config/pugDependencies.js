const fs = require('fs');
const path = require('path');
const workerFarm = require('worker-farm');

const { pages: devPage } = require('../options');


module.exports = async () => {
	const pages = fs.readdirSync(path.resolve(__dirname, '../src/pages/'));
	const workers = workerFarm(require.resolve('./compileFile.js'))
	const deps = [];
	if (devPage !== 'all') return [];
	pages.forEach((page, index) => {
		const path = `./src/pages/${page}/${page}.pug`;
		const promise = new Promise((resolve, reject) => {
			workers(path, (err, props) => {
				if (err) {
					reject(err)
				}
				resolve(props);
				if (index === pages.length - 1) {
					workerFarm.end(workers);
				}
			})
		})
		deps.push(promise);
	})
	return await Promise.all(deps);
}






