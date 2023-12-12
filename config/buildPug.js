const { pages: devPage } = require('../options');
const pug = require('pug');
const path = require('path');
const fs = require('fs');
const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const workerFarm = require('worker-farm');
const HtmlsWebpackPlugin = require('htmls-webpack-plugin');
const webpack = require('webpack');
module.exports = {
	pugBuilder: (options) => {

		let { smpMode, pages = [], callback = undefined } = options;

		console.log('\x1b[31m','====================\nhtml building....\n====================', '\x1b[0m');

		if (pages.length == 0) {
			pages = fs.readdirSync(path.resolve(__dirname, '../src/pages/')).filter((page) => {
				if (devPage === 'all') return true;
				return page == devPage;
			}).map((page) => path.parse(page).name);
		} else {
			pages = pages.map((page) => path.parse(page).name);
		}

		let smp = undefined;

		if (smpMode) {
			smp = new SpeedMeasurePlugin();
		}

		const workers = workerFarm(require.resolve('./pugRender.js'))

		const webpackConfig = {
			mode: 'development',
			entry: './src/index.js',
			output: {
				publicPath: '/',
				path: path.resolve(__dirname, '../html/'),
			},
			cache: true,
			devtool: false,
			module: {
				rules: [
					{
						test: /\.pug$/,
						exclude: /node_modules/,
						use: [

							{
								loader: "html-loader",
								options: {
									attributes: false//не учитываем default пути src для картинок и т.д.
								}
							},
							{
								loader: "pug-html-loader",
								options: {
									pretty: true//отключает компил html в одну строку
								}
							},
						]
					}
				]
			},
			optimization: {
			},
			plugins: [

				new HtmlsWebpackPlugin({
					htmls: [...pages.map((page, index) => {

						try {
							if (pages.length > 1) {
								// use worker-farm for parallel pug render
								return {
									src: `./src/pages/${page}/${page}.pug`,
									filename: `${page}.html`,
									async render() {
										const string = await new Promise((resolve, reject) => {
											try {
												workers(page, (err, render) => {
													if (err) {
														reject(err);
													};
													resolve(render);
													if (index === page.length - 1) {
														workerFarm.end(workers);
													}
												})
											} catch (err) {
												console.error('ERROR =>>>>>>>', err);
												workerFarm.end(workers);
											}

										})
										return string;
									}
								}
							} else {
								// worker-farm not needed for one page
								return {
									src: `./src/pages/${page}/${page}.pug`,
									filename: `${page}.html`,
									render: (file) => {
										try {
											return pug.renderFile(file, { pretty: true })
										} catch (err) {
											console.error('ERROR =>>>>>>>', err);
										}
									}
								}
							}
						} catch {
							throw new Error('PUG BUILD ERROR');
						}

					})]
				})

			]
		}

		webpack(smp ? smp.wrap(webpackConfig) : webpackConfig, (err, stats) => { // [Stats Object](#stats-object)
			if (err || stats.hasErrors()) {
				console.error(stats);
			}

			console.log('\x1b[32m','====================\nhtml builded\n====================', '\x1b[0m');

			if (callback) {
				callback();
			}

		})
	}
}