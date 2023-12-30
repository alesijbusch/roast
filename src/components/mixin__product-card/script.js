let productTileCardInit = () => {
  // product hover
  // let productCards = document.querySelectorAll(".product-card-wrap");
  // productCards.forEach((item) => {
  //   item.addEventListener("mouseenter", function (event) {
  //     let target = event.target,
  //       height = target.getBoundingClientRect().height,
  //       targetMiddle = target.querySelector(".product-card__info"),
  //       targetMiddleHeight = targetMiddle.getBoundingClientRect().height;

  //     target.setAttribute("style", `height: ${height}px;`);
  //     targetMiddle.setAttribute("style", `height: ${targetMiddleHeight}px;`);
  //     target.classList.add("hover");
  //   });
  //   item.addEventListener("mouseleave", function (event) {
  //     productCards.forEach((card) => {
  //       card.classList.remove("hover");
  //       card.setAttribute("style", `height: auto;`);
  //       card
  //         .querySelector(".product-card__info")
  //         .setAttribute("style", `height: auto;`);
  //     });
  //   });

  //   // item.addEventListener('click', function (){
  //   //     this.classList.toggle('clicked')
  //   // })
  // });

  // images gallery
  let allProductImages = document.querySelectorAll(".product-card__image");
  allProductImages.forEach((item) => {
    item.addEventListener("mouseenter", function (event) {
      let targetImg = event.target,
        groupParent = targetImg.closest(".js-pr-card-imgs"),
        groupImages = groupParent.querySelectorAll(".product-card__image"),
        dots = groupParent.querySelectorAll(".product-card__dots span");
      dots.forEach((dot) => {
        dot.classList.remove("active");
      });
      groupImages.forEach((img) => {
        img.classList.remove("active");
      });
      dots[targetImg.getAttribute("data-index")].classList.add("active");
      targetImg.classList.add("active");
    });
    item.addEventListener("mouseleave", function (event) {
      let groupParent = event.target.closest(".js-pr-card-imgs"),
        groupImages = groupParent.querySelectorAll(".product-card__image"),
        dots = groupParent.querySelectorAll(".product-card__dots span");
      dots.forEach((dot) => {
        dot.classList.remove("active");
      });
      groupImages.forEach((img) => {
        img.classList.remove("active");
      });
      dots[0].classList.add("active");
      groupImages[0].classList.add("active");
    });
  });
};

window.addEventListener("load", function () {
  productTileCardInit();
});

window.addEventListener("change-product-card", function ($event) {
  productTileCardInit();
});

window.addEventListener("ajax-finished", function ($event) {
  productTileCardInit();
});
