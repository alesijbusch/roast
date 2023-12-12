window.addEventListener('load', function () {
    const config = {
        root: null, // Sets the framing element to the viewport
        rootMargin: "0px",
        threshold: 0.5
    }


    let container = document.querySelectorAll('.js-splide-viewed');
    container.forEach(item => {
        const observer = new IntersectionObserver((entries) => {
            if(item.classList.contains('inited')) {
                return
            }
            let slidesCount = item.querySelectorAll('.splide__slide').length;

            if (entries[0].isIntersecting) {
                let splide = new Splide( '.js-splide-viewed', {
                    type: 'slide',
                    pagination: false,
                    perPage: 6,
                    drag: 'free',
                    speed: 1200,
                    gap: 24,
                    lazyLoad: true,
                    breakpoints: {
                        1199: {
                            perPage: 5,
                        },
                        991: {
                            perPage: 4,
                        },
                        767: {
                            perPage: 3,
                            arrows: false
                        },
                        600: {
                            autoWidth: true,
                        },
                    }
                }).mount();

                item.classList.add('inited');
            }

        }, config);
        observer.observe(item);
    });

    console.log('init')

})

