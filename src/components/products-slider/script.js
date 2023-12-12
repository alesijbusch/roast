window.addEventListener('load', function () {




    const config = {
        root: null, // Sets the framing element to the viewport
        rootMargin: "200px",
        threshold: 0.5
    }


    const sliderObserver = new IntersectionObserver((entries, observer) =>{
        entries.forEach(el=>{
            if(el.isIntersecting){
                if(el.target.classList.contains('inited')) {
                    return
                }

                let slidesCount = el.target.querySelectorAll('.splide__slide').length;


                new Splide( el.target, {
                    type: 'slide',
                    pagination: false,
                    perPage: 5,
                    drag: true,
                    gap: 24,
                    speed: 1200,
                    lazyLoad: true,
                    breakpoints: {
                        1199: {
                            perPage: 4,
                            drag: slidesCount > 4,
                        },
                        991: {
                            perPage: 3,
                            drag: slidesCount > 3,
                        },
                        767: {
                            perPage: 2,
                            drag: slidesCount > 2,
                        },
                        560: {
                            perPage: 1,
                            arrows: false,
                            autoWidth: true,
                            drag: true
                        },
                    }
                }).mount();

                el.target.classList.add('inited');

            }
        }, config)
    })

    let slider = document.querySelectorAll('.js-splide-products');

    slider.forEach(el=>{

        sliderObserver.observe(el)
    })

    const sliderSmObserver = new IntersectionObserver((entries, observer) =>{
        entries.forEach(el=>{
            if(el.isIntersecting){
                if(el.target.classList.contains('inited')) {
                    return
                }

                let slidesCount = el.target.querySelectorAll('.splide__slide').length;


                new Splide( el.target, {
                    type: 'slide',
                    pagination: false,
                    perPage: 4,
                    drag: true,
                    gap: 24,
                    speed: 1200,
                    lazyLoad: true,
                    breakpoints: {
                        1199: {
                            perPage: 4,
                            drag: slidesCount > 4,
                        },
                        991: {
                            perPage: 3,
                            drag: slidesCount > 3,
                        },
                        767: {
                            perPage: 2,
                            drag: slidesCount > 2,
                        },
                        560: {
                            perPage: 1,
                            arrows: false,
                            autoWidth: true,
                            drag: true
                        },
                    }
                }).mount();

                el.target.classList.add('inited');
                
            }
        }, config)
    })

    let sliderSm = document.querySelectorAll('.js-splide-products-sm');

    sliderSm.forEach(el=>{

        sliderSmObserver.observe(el)
    })




    let container2 = document.querySelectorAll('.js-splide-products-sm');
    container2.forEach(item => {
        const observer = new IntersectionObserver((entries) => {
            if(item.classList.contains('inited')) {
                return
            }
            let slidesCount = item.querySelectorAll('.splide__slide').length;

            if (entries[0].isIntersecting) {
                let splide = new Splide( '.js-splide-products-sm', {
                    type: 'slide',
                    pagination: false,
                    perPage: 4,
                    drag: slidesCount > 4,
                    gap: 24,
                    speed: 1200,
                    lazyLoad: true,
                    breakpoints: {
                        1199: {
                            perPage: 4,
                            drag: slidesCount > 4,
                        },
                        991: {
                            perPage: 3,
                            drag: slidesCount > 3,
                        },
                        767: {
                            perPage: 2,
                            drag: slidesCount > 2,
                        },
                        600: {
                            arrows: false,
                            autoWidth: true,

                        },

                    }
                }).mount();

                item.classList.add('inited');
            }

        }, config);
        observer.observe(item);
    });
})

