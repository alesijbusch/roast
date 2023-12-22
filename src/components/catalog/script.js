const filtersPanelLists = document.querySelectorAll("#catalog-ics__list-js");
filtersPanelLists.forEach(function (list) {
  const filtersPanelItems = list.querySelectorAll(".ics-item");
  const filtersPanelShow = list.querySelector(".ics-item__more");
  const numVisibleItems = window.innerWidth >= 768 ? 9 : 3;
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
        filtersPanelShow.innerHTML = `<span>Еще</span> <svg xmlns="http://www.w3.org/2000/svg" width="13" height="4" viewBox="0 0 13 4" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.95632 3.31962C2.68511 3.31962 3.27592 2.72881 3.27592 2.00002C3.27592 1.27122 2.68511 0.68042 1.95632 0.68042C1.22752 0.68042 0.636719 1.27122 0.636719 2.00002C0.636719 2.72881 1.22752 3.31962 1.95632 3.31962ZM7.67458 2.00002C7.67458 2.72881 7.08377 3.31962 6.35498 3.31962C5.62619 3.31962 5.03538 2.72881 5.03538 2.00002C5.03538 1.27122 5.62619 0.68042 6.35498 0.68042C7.08377 0.68042 7.67458 1.27122 7.67458 2.00002ZM12.0732 2.00002C12.0732 2.72881 11.4824 3.31962 10.7536 3.31962C10.0248 3.31962 9.43404 2.72881 9.43404 2.00002C9.43404 1.27122 10.0248 0.68042 10.7536 0.68042C11.4824 0.68042 12.0732 1.27122 12.0732 2.00002Z" fill="#8A6048"/>
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
