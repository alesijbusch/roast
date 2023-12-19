"use strict";

if (document.documentElement.clientWidth <= 600.75) {
  const lists = document.querySelectorAll("#tizers-section_list");
  lists.forEach(function (list) {
    const items = list.querySelectorAll(".ts-item");
    const spinner = list.querySelector(".tizers-section_btn .btn");
    console.log(items);
    if (items.length > 3) {
      let listVisible = false;
      for (let i = 3; i < items.length; i++) {
        items[i].style.display = "none";
        spinner.textContent = `Показать все ${items.length - 3}`;
      }
      spinner.addEventListener("click", function () {
        if (listVisible) {
          for (let i = 3; i < items.length; i++) {
            items[i].style.display = "none";
          }

          listVisible = false;
        } else {
          for (let i = 3; i < items.length; i++) {
            items[i].style.display = "flex";
          }
          spinner.style.display = "none";
          listVisible = true;
        }
      });
    }
  });
}
