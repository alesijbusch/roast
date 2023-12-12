window.addEventListener('load', function () {
    // плавная анимация высоты
    let head = document.querySelectorAll('.slide-block__head');

    head.forEach(btn => {
        let body = btn.closest('.slide-block').querySelectorAll('.slide-block__body')[0];
        btn.addEventListener("click", () => {
            if (body.style.height === "0px") {
                body.style.height = `${ body.scrollHeight }px`
            } else {
                body.style.height = `${ body.scrollHeight }px`;
                window.getComputedStyle(body, null).getPropertyValue("height");
                body.style.height = "0";
            }
        });
    });
})

