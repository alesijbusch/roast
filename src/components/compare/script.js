window.addEventListener('load', function () {

    const sliderParams = {
        type: 'slide',
        pagination: false,
        perPage: 5,
        drag: true,
        speed: 400,
        lazyLoad: false,
        breakpoints: {
            1199: {
                perPage: 4,
            },
            991: {
                perPage: 3,
            },
            767: {
                perPage: 2,
            },
        }
    }

    const compareTable = document.querySelector('.js-splide-compare-products');
    const compareProps = document.querySelector('.js-splide-compare-col');
    const slideTop = new Splide( compareTable, sliderParams);

    sliderParams.arrows = false;
    const slideBottom = new Splide( compareProps, sliderParams);

    slideTop.sync(slideBottom);
    slideTop.mount();
    slideBottom.mount();

    slideTop.on( 'moved', function (newIndex) {
        console.log('newIndex: ' + newIndex)
    } );
})