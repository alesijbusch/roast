window.addEventListener("load", function () {
	const config = {
		root: null, // Sets the framing element to the viewport
		rootMargin: "100px",
		threshold: 0.1,
	};

	let container = document.querySelectorAll(".about-slider");


	container.forEach((item) => {
		const observer = new IntersectionObserver((entries) => {
			if (item.classList.contains("inited")) {
				return;
			}
			if (entries[0].isIntersecting) {
				console.log(container)

				let main = new Splide(item.querySelector('.about-slider__main'), {

					lazy: 'sequential',
					// rewind: true,
					pagination: false,
					arrows: true,
					gap: 10,
				});

				let thumbnails = new Splide(item.querySelector('.about-slider__thumbnail'), {
					fixedWidth: 135,
					lazy: 'sequential',
					fixedHeight: 90,
					gap: 10,
					// rewind: true,
					pagination: false,
					isNavigation: true,
					breakpoints: {
						1200: {
							fixedWidth: 90,
							fixedHeight: 55,
						},
						600: {
							fixedWidth: 60,
							fixedHeight: 40,
							gap: 5
						},
					},
				});

				main.sync(thumbnails);
				main.mount();
				thumbnails.mount();


				item.classList.add("inited");
			}
		}, config);
		observer.observe(item);
	});
});
