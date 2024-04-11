window.addEventListener("load", function () {
  let splide = new Splide(".js-splide-banners", {
    type: "loop",
    arrows: false,
    pagination: true,
    perPage: 1,
    perMove: 1,
    drag: true,
    speed: 1000,
    lazyLoad: "nearby",
    breakpoints: {
      768: {
        arrows: false,
      },
    },
  }).mount();

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
      console.log('here')
    document.querySelector('.slider-product-page .item').innerHTML = ni + 1



  });
  splide.on("drag", () => {
    loadSlide(".splide__slide");
  });

  let splideProduct = new Splide(".js-splide-slider-product", {
    type: "slide",
    arrows: true,
    pagination: false,
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
  // splideProduct.on("pagination:mounted", function (data) {

  //   data.list.classList.add("splide__pagination--custom");

  //   data.items.forEach(function (item, _, items) {
  //     item.button.textContent = String(item.page + 1);
  //     console.log(data);
  //     console.log(String(items.length));
  //     items.innerHTML = `<span>${String(items.length)}</span>`;

  //   });

  // });
    splideProduct.on("move", (ni, prev, dest) => {

        document.querySelector('.slider-product-page .item').innerHTML = ni + 1

    });

  splideProduct.mount();
});
