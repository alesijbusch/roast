const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const globImporter = require('node-sass-glob-importer');
const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const filename = (ext, directory) =>
	isDevMode
		? `${directory}/[name].min.${ext}`
		: `${directory}/[name].min.${ext}`;
const isDevMode = process.env.MODE === 'development';
module.exports = {

	criticalBuild: (options) => {

		const {smpMode = false, callback } = options;


		const smp = new SpeedMeasurePlugin();

		const webpackConfig = {
			entry: {
				critical: path.resolve(__dirname, '../src/webpack_lists/critical_css.scss'),
			},
			output: {
				publicPath: '/',
				path: path.resolve(__dirname, '../html/'),
				filename: filename('js', 'dev_files'),
			},
			devtool: false,
			mode: 'production',
			module: {
				rules: [
					{
						test: /\.s?css$/i,
						exclude: /node_modules/,
						use: [
							MiniCssExtractPlugin.loader,
							{
								loader: 'css-loader',
								options: {
									url: false
								}
							},
							{
								loader: 'sass-loader',
								options: {
									sassOptions: {
										webpackImporter: false,
										importer: globImporter(),
										import: false
									}
								}
							}
						]
					},
				]
			},
			plugins: [
				new MiniCssExtractPlugin({
					filename: 'critical.css',
				})
			]
		}


		webpack(smpMode? smp.wrap(webpackConfig) : webpackConfig, (err, stats) => { // [Stats Object](#stats-object)
			if (err || stats.hasErrors()) {
				console.error(stats);
			}
			console.log('\x1b[32m','====================\nCRITICAL BUILDED\n ====================', '\x1b[0m');
			if (callback) {
				callback();
			}

		})
	}
};