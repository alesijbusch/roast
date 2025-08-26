window.addEventListener("load", function () {

	let time = 5000

	let splide = new Splide(".js-splide-banners", {
		type: "loop",
		arrows: false,
		pagination: true,
		perPage: 1,
		perMove: 1,
		autoplay: true, // Включаем автоплей
		interval: time, // 5 секунд = 5000 миллисекунд
		// pauseOnHover: true,
		drag: true,
		speed: 1000,
		lazyLoad: "nearby",
		breakpoints: {
			768: {
				arrows: false,
			},
		},
	})
	// splide.on( 'mounted', function () {
	// 	document.querySelector('.js-splide-banners').style.setProperty('--transition', `all linear ${time/1000}s.` )
	// } );

		splide.mount();



	let loadSlide = (names) => {
		let activeSlides = document.querySelectorAll(`.js-splide-banners ${names}`);

		activeSlides.forEach((slide) => {
			let dataSrcset = slide.querySelectorAll("[data-srcset]");
			let dataSrc = slide.querySelectorAll("[data-src]");

			dataSrcset.forEach((item) => {
				item.setAttribute("srcset", item.getAttribute("data-srcset"));
			});
			dataSrc.forEach((item) => {
				item.setAttribute("src", item.getAttribute("data-src"));
				item.classList.add("loaded");
			});
		});
	};


	splide.on("move", (ni, prev, dest) => {
		loadSlide(".splide__slide");
		// document.querySelector('.slider-product-page .item').innerHTML = ni + 1


	});
	splide.on("drag", () => {
		loadSlide(".splide__slide");
	});

	let splideProduct = new Splide(".js-splide-slider-product", {
		type: "slide",
		arrows: true,
		pagination: true,
		perPage: 1,
		perMove: 1,
		speed: 500,
		lazyLoad: "nearby",
		// breakpoints: {
		//   768: {
		//     arrows: false,
		//   },
		// },
	});


	if (window.innerWidth < 1024) {

		const slider = document.querySelector('.slider-product')
		if (!slider) return

		const options = {
			root: null, // Sets the framing element to the viewport
			rootMargin: "0px",
			threshold: 0.5,
		};

		const t = document.querySelectorAll('.slider-product__bottom')

			t.forEach((el) => {
				const observer = new IntersectionObserver((entries, observer) => {
					if (entries[0].isIntersecting) {
						slider.classList.add('in-view')
					} else {
						slider.classList.remove('in-view')

					}
				}, options);
				observer.observe(el);
			});
	}


	splideProduct.mount();
});
