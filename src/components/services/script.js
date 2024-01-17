window.addEventListener("load", function () {

    const config = {
        root: null, // Sets the framing element to the viewport
        rootMargin: "100px",
        threshold: 0.5,
    };

    const sliderObserver = new IntersectionObserver((entries, observer) => {
        console.log(entries);
        entries.forEach((el) => {
            if (el.isIntersecting) {
                if (el.target.classList.contains("inited")) {
                    return;
                }

                let slidesCount = el.target.querySelectorAll(".splide__slide").length;

                new Splide(el.target, {
                    type: "slide",
                    pagination: false,
                    perPage: 4,
                    padding: 10,
                    drag: slidesCount > 4,
                    gap: 20,
                    lazyLoad: 'sequential',
                    breakpoints: {
                        991: {
                            padding: 0,
                            perPage: 3,
                            drag: slidesCount > 3,

                        },
                        768: {
                            perPage: 2,
                            drag: slidesCount > 2,

                        },
                        540: {
                            perPage: 1,
                            drag: slidesCount > 1,

                        },

                    },
                }).mount();

                el.target.classList.add("inited");
            }
        }, config);
    });

    let slider = document.querySelectorAll(".js-splide-service");

    slider.forEach((el) => {
        sliderObserver.observe(el);
    });





});
