const path = require('path');
const webpack = require('webpack');
const glob = require('glob');
const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const HtmlsWebpackPlugin = require('htmls-webpack-plugin');
const pug = require('pug');
module.exports = {
	ajaxPugBuilder: (options) => {

		const  {smpMode, init = false, callback = false} = options;


		if(!init){
			console.log('\x1b[31m', 'ajax html building....');
		}

		const runWebpack = (components) => {

			let smp = undefined;

			if(smpMode){
				 smp = new SpeedMeasurePlugin();
			}

			const webpackConfig = 	{
				mode: 'production',
				entry: './src/index.js',
				output: {
					publicPath: '/',
					path: path.resolve(__dirname, '../html/components-template/'),
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
										data: {
											dev_mode: isDevMode ? 'dev' : 'prod' //прокидываем переменную  непосредственно в PUG и включаем/выключаем подключение css, js на страницу  в зависимости от prod/dev
										},
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
						htmls: [...components.map((component, index) => {
							return {
								src: component,
								filename: component.replace('src/components/', '').replace('.pug', '.html'),
								render: (file) => {
									try {
										return pug.renderFile(file, { pretty: true })
									} catch (err) {
										console.error('ERROR =>>>>>>>', err);
									}
								}
							}
						})]
					})

				]
			}

			
			webpack(smp ? mp.wrap(webpackConfig) : webpackConfig, (err, stats) => {
					if (err || stats.hasErrors()) {
						console.error(stats);
					}

					if(!init){
						console.log('\x1b[36m%s\x1b[0m', 'ajax html builded');
					}


					if (callback) {
						callback();
					}

				})
		}


			glob("src/components/**/ajax__*.pug", function (er, files) {
				runWebpack(files);
			});

			const isDevMode = false;
	}
}