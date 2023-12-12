export default () => {
	(function () {
		let callback_loaded = function (el) {
			const img_wrap = el?.closest('.lazy-img-wrap');
			img_wrap?.classList.add('loaded');
		};

		new LazyLoad({
			elements_selector: ".lazy-img",
			threshold: 0,
			callback_loaded: callback_loaded,
		});

        new LazyLoad({
            elements_selector: ".lazy-video:not(.lazy-video-scroll)",
            threshold: 0,
            callback_loaded: function (el) {
                // el.play()
            }
        });



	})();
}
//
// export default () => {
//     new LazyLoad({
//         elements_selector: ".lazy-video:not(.lazy-video-scroll)",
//         threshold: 0,
//         callback_loaded: function (el) {
//             // el.play()
//         }
//     });
//
//
//     //TODO: rewrite on pure js
//
//     // $('.lazy-video-scroll').each(function () {
//     // 	const $this = $(this);
//     // 	window.oneevent({
//     // 		name: 'lazy-video-scroll',
//     // 		event: {
//     // 			click: true,
//     // 			timeout: {
//     // 				delay: 6000
//     // 			},
//     // 		},
//     // 		callback: () => {
//     // 			const src = $this.attr('data-src');
//     // 			$this.attr('src', src);
//     // 		}
//     // 	})
//     // })
// };
//

