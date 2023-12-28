window.addEventListener("load", function () {
  document
    .querySelectorAll(".product-block__grid__item-line")
    .forEach((block) => {
      const percent = block.getAttribute("data-percent");
      block.style.background = `linear-gradient(90deg, black ${percent}%, #e3e3e3 ${percent}%)`;
    });
});
