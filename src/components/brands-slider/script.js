window.addEventListener('load', function () {
    const config = {
        root: null, // Sets the framing element to the viewport
        rootMargin: "0px",
        threshold: 0.5
    }

    let container = document.querySelectorAll('.js-splide-brands');
    container.forEach(item => {
        const observer = new IntersectionObserver((entries) => {
            if(item.classList.contains('inited')) {
                return
            }
            if (entries[0].isIntersecting) {
                let splide = new Splide( '.js-splide-brands', {
                    type: 'loop',
                    height: '86px',
                    autoWidth: true,
                    drag: 'free',
                    pagination: false,
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

