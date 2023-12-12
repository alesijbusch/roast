const options = require('../options')
const glob = require('glob');
const fs = require('fs');
const path = require('path');

const pagesNames = options.pages === 'all' ? '*' : options.pages;
const pagesGlob = glob.sync(`html/${pagesNames}.html`);

pagesGlob.forEach(page => {
	let scripts = [];
	let html = fs.readFileSync(page, 'utf8')
	const linkRegExp = /<script src=".*\.js.*<\/script>/g
	const links = html.match(linkRegExp);
	if (!links) return console.log('no script tags')
	const entry = links.map(link => {
		html = fs.readFileSync(page, 'utf8');
		const newHTML = html.replace(link, ``);
		fs.writeFileSync(page, newHTML);
		const path = `${link.match(/src=".*\.js/g)[0].replace('src="./', '')}`
		return path;
	})
	entry.forEach(script => {
		const pathToScript = path.resolve(__dirname, `../html/${script}`);
		console.log('path', pathToScript);
		const res = fs.readFileSync(pathToScript, 'utf-8');
		scripts.push(res);
	})
	const folder = path.resolve(__dirname, `../html`);
	const directory = fs.readdirSync(folder, 'utf-8');
	if (!directory.includes('combinedJS')) {
		fs.mkdirSync(`${folder}/combinedJS`);
	}
	fs.writeFileSync(`${folder}/combinedJS/${page.replace('html/', '')}.js`, scripts.join(''))
	html = fs.readFileSync(page, 'utf-8');
	const replaceItem = html.match(/<script data="component"><\/script>/);
	const newHtml = html.replace(replaceItem, `<script src="./combinedJS/${page.replace('html/', '')}.js" defer></script>`);
	fs.writeFileSync(page, newHtml);
	scripts = [];
})


