const rollup = require('rollup');
const babel = require('rollup-plugin-babel');
const commonjs = require('@rollup/plugin-commonjs');
const minify = require('rollup-plugin-babel-minify');
const cleanup = require('rollup-plugin-cleanup');

module.exports = {



	buildJs: (params) => {

		return new Promise ((resolve, reject) => {
			const { input, output, init = false } = params;

			const inputOptions = {
				input: input,
				plugins: [
					// resolve(),
					cleanup(),
					commonjs(),
					babel(),
					minify(),
				]
			};
			const outputOptions = {
				file: output,
				format: 'iife'
			};
	
			async function build() {
				// create a bundle
				if(!init){
					console.log('\x1b[31m','====================\njs building....\n ====================', '\x1b[0m');
					// console.log('\x1b[31m', 'js building....');
				}
	
				const bundle = await rollup.rollup(inputOptions);
				await bundle.generate(outputOptions);
	
				// or write the bundle to disk
				await bundle.write(outputOptions);
	
				// closes the bundle
				await bundle.close();

				resolve();
	
				if(!init){
					console.log('\x1b[32m','====================\njs builded\n'+ output +'\n ====================', '\x1b[0m');
				}
	
			}

			build();
		});
	},
};