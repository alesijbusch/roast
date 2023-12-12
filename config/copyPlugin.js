
const CopyWebpackPlugin = require('copy-webpack-plugin');
const webpack = require('webpack');
const path = require('path');
module.exports = {


	copyPlugin: async (options) => {

		const  {vendor = false, jquery = false, images = false, fonts = false, favicons = false} = options;

		return new Promise((resolve, reject) => {

			const CopyWebpackPluginConf = () => {

				const configs = []

				const vendorConfig = {
					from: 'vendor/*.js',
					context: './src/assets/js/',
					to: path.resolve(__dirname, '../html/js/')
				} 

				if(vendor){
					configs.push(vendorConfig)
				} 

				const jqueryConfig = 	{
					from: 'alpinejs/*.js',
					context: './src/assets/js/',
					to: path.resolve(__dirname, '../html/js/')
				} 

				if(jquery){
					configs.push(jqueryConfig)
				}

				const imagesConfig =  {
					from: '**/*',
					to: 'images',
					context: './src/assets/images/',
					noErrorOnMissing: true
				} 

				if(images){
					configs.push(imagesConfig)
				}

				const fonstConfig = {
					from: '**/*',
					to: 'fonts',
					context: './src/assets/fonts/',
					noErrorOnMissing: true
				}

				if(fonts){
					configs.push(fonstConfig)
				}

				const faviconsConfig = {
					from: '**/*',
					to: 'favicons',
					context: './src/assets/favicons/',
					noErrorOnMissing: true
				}

				if(favicons){
					configs.push(faviconsConfig)
				}

				return [
					...configs
				]
			}
	
			webpack(
				{
					entry: './src/index.js',
					output: {
						publicPath: '/',
						path: path.resolve(__dirname, '../html/'),
					},
					devtool: false,
					mode: 'production',
					plugins: [
						new CopyWebpackPlugin({
							patterns: CopyWebpackPluginConf()
						}),
					]
				}, (err, stats) => {
					if (err || stats.hasErrors()) {
						console.error(stats);
						reject(err);
					}
					// console.log('\x1b[36m%s\x1b[0m', 'copyPluginEnd');
					resolve();
				})
		})

	}
}
