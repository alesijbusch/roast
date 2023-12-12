window.addEventListener('load', function () {
	let thumbsSlidesCount = document.querySelectorAll('#thumbnail-carousel .splide__slide').length;
	let mainCarousel = document.querySelector('#main-carousel');
	if (mainCarousel) {
		let main = new Splide('#main-carousel', {
			type: 'slide',
			pagination: false,
			lazyLoad: true,
			perPage: 1,
			breakpoints: {
				767: {
					arrows: false,
					pagination: true,
                    speed: 600,
				},
			}
		});
		let thumbnails = new Splide('#thumbnail-carousel', {
			gap: 4,
			rewind: true,
			pagination: false,
			direction: 'ttb',
			isNavigation: true,
			lazyLoad: true,
			// heightRatio: 1,
			height: '100%',
			perPage: 6,
			drag: thumbsSlidesCount > 6,
		});

		main.sync(thumbnails);
		main.mount();
		thumbnails.mount();
	}


})

