window.addEventListener("load", function () {
  const config = {
    root: null, // Sets the framing element to the viewport
    rootMargin: "0px",
    threshold: 0.5,
  };

  let container = document.querySelectorAll(".js-splide-brands");
  container.forEach((item) => {
    const observer = new IntersectionObserver((entries) => {
      if (item.classList.contains("inited")) {
        return;
      }
      if (entries[0].isIntersecting) {
        let splide = new Splide(".js-splide-brands", {
          type: "slide",
          height: "80px",
          gap: 65,
          perPage: 6,
          // autoWidth: true,
          drag: "free",
          pagination: false,
          lazyLoad: "sequential",
          breakpoints: {
            1199: {
              perPage: 5,
              arrows: true,
            },
            991: {
              perPage: 4,
              arrows: true,
              gap: 30,
            },
            767: {
              perPage: 3,
              arrows: true,
              gap: 15,
            },
            560: {
              perPage: 2.5,
              arrows: false,
              gap: 10,
              drag: true,
            },
          },
        }).mount();

        item.classList.add("inited");
      }
    }, config);
    observer.observe(item);
  });
});
