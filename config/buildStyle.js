const path = require('path');
const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const webpack = require('webpack');


module.exports = {
	styleBuilder: (options) => {
		const { fileName: link, init, callback = null, smpMode = false } = options;
		return new Promise((resolve, reject) => {
			if (!init) {
				console.log('\x1b[31m','====================\nstyle building....\n ====================', '\x1b[0m');
			}
			const input = './' + link.split('\\').join('/');
			const output = link.split('\\').join('/').replace('src/components', '').replace('scss', 'css');

			let smp = undefined;

			if(smpMode){
				smp = new SpeedMeasurePlugin();
			}


			const webpackConfig =
				{
					mode: 'development',
					entry: input,
					output: {
						publicPath: '/',
						path: path.resolve(__dirname, '../html/'),
					},
					cache: true,
					devtool: false,
					module: {
						rules: [
							{
								test: /\.s?css$/i,
								exclude: /node_modules/,
								use: [
									{
										loader: 'file-loader',
										options: {
											name: `components-template/${output}`,
										}
									},
									{
										loader: 'extract-loader'
									},
									{
										loader: 'css-loader',
										options: {
											url: false,
										},
									},
									{
										loader: 'sass-loader',
										options: {
											sassOptions: {
												webpackImporter: false,
												// importer: globImporter(),
												import: false
											}
										}
									},
								]
							},
						]
					},
					optimization: {
					},
					plugins: [

					]
				}

			webpack(smp ? smp.wrap(webpackConfig) : webpackConfig, (err, stats) => { // [Stats Object](#stats-object)
				if (err || stats.hasErrors()) {
					console.error(stats);
				}
				if (!init) {
					console.log('\x1b[32m','====================\nstyle builded\n'+ output +'\n ====================', '\x1b[0m');
				}
				resolve();
				if (callback) {
					callback();
				}

			})
		})


	}
}