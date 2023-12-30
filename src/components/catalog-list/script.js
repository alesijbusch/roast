const filtersPanelLists = document.querySelectorAll(".catalog-list__menu-js ");
filtersPanelLists.forEach(function (list) {
  const filtersPanelItems = list.querySelectorAll(".catalog-list__menu-item");
  const filtersPanelShow = list.querySelector(".catalog-list__menu-more");
  const numVisibleItems = window.innerWidth >= 768 ? 6 : 4;
  if (filtersPanelItems.length > numVisibleItems) {
    filtersPanelShow.style.display = "flex";
    let listVisible = false;
    for (let i = numVisibleItems; i < filtersPanelItems.length; i++) {
      filtersPanelItems[i].style.display = "none";
    }
    filtersPanelShow.addEventListener("click", function () {
      if (listVisible) {
        for (let i = numVisibleItems; i < filtersPanelItems.length; i++) {
          filtersPanelItems[i].style.display = "none";
        }
        filtersPanelShow.innerHTML = `<span>Еще</span> <svg xmlns="http://www.w3.org/2000/svg" width="8" height="6" viewBox="0 0 8 6" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.28033 0.987309C0.987437 0.694416 0.512563 0.694416 0.21967 0.987309C-0.0732233 1.2802 -0.0732233 1.75508 0.21967 2.04797L3.15097 4.97927C3.16106 4.99065 3.17155 5.0018 3.18245 5.0127C3.356 5.18625 3.59345 5.25696 3.81907 5.22484C3.97482 5.20303 4.12504 5.13223 4.24482 5.01246C4.25425 5.00302 4.26337 4.9934 4.2722 4.98361L7.20759 2.04821C7.50049 1.75532 7.50049 1.28045 7.20759 0.987553C6.9147 0.69466 6.43983 0.69466 6.14693 0.987553L3.71375 3.42073L1.28033 0.987309Z" fill="#8A6048"/>
      </svg>`;

        listVisible = false;
      } else {
        for (let i = numVisibleItems; i < filtersPanelItems.length; i++) {
          filtersPanelItems[i].style.display = "flex";
        }
        filtersPanelShow.innerHTML = `<span>Скрыть</span> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M7.8534 8.05946L4.44922 4.65527L3.38856 5.71593L6.79274 9.12012L3.38856 12.5243L4.44922 13.585L7.8534 10.1808L11.3886 13.7159L12.4492 12.6553L8.91406 9.12012L12.4492 5.58496L11.3886 4.5243L7.8534 8.05946Z" fill="#896652"/>
      </svg>`;
        listVisible = true;
      }
    });
  }
});
