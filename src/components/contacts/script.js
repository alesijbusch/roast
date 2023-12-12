window.addEventListener('load', function () {
    const config = {
        root: null, // Sets the framing element to the viewport
        rootMargin: "0px",
        threshold: 0.5
    }

    let container = document.querySelectorAll('.js-splide-contacts');
    container.forEach(item => {
        const observer = new IntersectionObserver((entries) => {
            if(item.classList.contains('inited')) {
                return
            }
            if (entries[0].isIntersecting) {
                let splide = new Splide( '.js-splide-contacts', {
                    type: 'loop',
                    pagination: true,
                    lazyLoad: 'sequential',
                    breakpoints: {
                        600: {
                            arrows: false
                        },
                    }
                }).mount();

                item.classList.add('inited');
            }

        }, config);
        observer.observe(item);
    });
})

