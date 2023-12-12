const Imagemin = require('imagemin');
const webp = require("imagemin-webp")
const path = require('path');
const fs = require('fs');


(async () => {

	const readDir = () => {
		return new Promise((resolve, reject) => {
			fs.readdir(path.resolve(__dirname, '../src/assets/images/'), (err, data) => {
				if (data) {
					resolve(data)
				} else {
					reject(err);
				}
			});
		})
	}

	const dirBefore = await readDir();

	dirBefore.forEach(element => {
		if (element.match(/(\.jpg|\.png)/g)) {
			fs.copyFile(path.resolve(__dirname, `../src/assets/images/${element}`), path.resolve(__dirname, `../src/assets/images/${element}.${element.split('.')[1]}`), (err) => {
				if (err) {
					console.log("Error Found:", err);
				}
			});
		}
	});

	await Imagemin(['./src/assets/images/*.{jpg,png}.{jpg,png}'], {
		destination: path.resolve(__dirname, '../html/images'),
		plugins: [
			webp({ quality: 90 })
		]
	})

	const dirAfter = await readDir();

	dirAfter.forEach(element => {
		if (element.match(/(\.jpg|\.png){2}/g)) {
			fs.unlink(path.resolve(__dirname, `../src/assets/images/${element}`), err => {
				if (err) {
					console.log("Error Found:", err);
				}
			})
		}
	});
})();

