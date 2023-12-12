const imagemin = require('imagemin');
const imageminJpegtran = require('imagemin-jpegtran');


(async function() {
    const files = await imagemin(['../src/assets/images/*.{jpg,png}'], {
        destination: '../html/images/opt/',
        plugins: [
            imageminJpegtran(),
        ]
    });
    console.log(files);
})();