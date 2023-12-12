const path = require('path');
const SpriteLoaderPlugin = require('svg-sprite-loader/plugin');

module.exports = [
	{
		entry: {
			sprite: path.resolve(__dirname, "../src/webpack_lists/sprites.js"),
		},
		output: {
			// publicPath: ASSET_PATH,
			path: path.resolve(__dirname, '../html/images/'),
			filename: '[name]/script.min.js'
		},
		module: {
			rules: [
				{
					test: /\.svg$/,
					// include: ico,
					use: [
						{
							loader: 'svg-sprite-loader',
							options: {
								extract: true,
								spriteFilename: "sprite.svg",
								runtimeCompat: true,
								// outputPath: path.resolve(__dirname, '../html/image'),
							}
						},
						{
							loader: 'svgo-loader',
							options: {
								plugins: [
									{
										"name": "removeAttrs",
										"params": {
											attrs: '(fill)'
										}
									}
								]
							}
						}
					]
				},
			]
		},
		plugins: [
			new SpriteLoaderPlugin({
				plainSprite: true,
			}),
		]
	},
];