window.addEventListener('load', function () {
    // бургер
    let catalogBurger = document.querySelectorAll('.header__main-burger');
    catalogBurger[0].addEventListener('click', function (ev) {
        ev.stopPropagation();
        ev.preventDefault();
    });

    // меню на адаптиве
    let checkWidths = () => {
        let headerTop = document.querySelector('.header__top-inner'),
            headerTopWidth = headerTop.clientWidth,
            headerTopItems = document.querySelectorAll('.header__top-inner > *'),
            items = document.querySelectorAll('.header__top-nav .nav-item--last'),
            moreNav = document.querySelector('.js-nav-lvl2-list--more');

        let itemsWidth = 0;
        headerTopItems.forEach(item => {
            itemsWidth = itemsWidth + item.clientWidth;
        });

        if (headerTopWidth < itemsWidth + 10) {
            headerTop.classList.add('is-more');
            items.forEach(li => {
                moreNav.appendChild(li);
            })

        } else {
            headerTop.classList.remove('is-more');
        }
    }
    checkWidths();

    // плавная анимация высоты скрытого лвла меню
    let more = document.querySelectorAll('.header-menu .more');
    let menu = document.querySelectorAll('.header-menu .collapsed');
    more.forEach(btn => {
        let li = btn.closest('ul').querySelectorAll('.collapsed');
        btn.addEventListener("click", () => {
            li.forEach(item => {
                if (item.style.height === "0px" || !item.style.height) {
                    item.style.height = `${ item.scrollHeight }px`
                } else {
                    item.style.height = `${ item.scrollHeight }px`;
                    window.getComputedStyle(item, null).getPropertyValue("height");
                    item.style.height = "0";
                }
            })
        });
    });
    menu.forEach(menuItem => {
        menuItem.addEventListener("transitionend", () => {
            if (!menuItem.style.height) {
                menuItem.style.height = "0px"
            }
            if (menuItem.style.height !== "0px") {
                menuItem.style.height = "auto"
            }
        });
    });

    // убираем переход по ссылке при клике по стрелке
    let menuIcons = document.querySelectorAll('.header-menu a > .icon-wrap');
    menuIcons.forEach(icon => {
        icon.addEventListener('click', function (ev) {
            ev.preventDefault();
        })
    })





})

