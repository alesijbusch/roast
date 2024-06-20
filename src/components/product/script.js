window.addEventListener("load", function () {
    let main = new Splide("#main-carousel", {
        type: "slide",
        pagination: true,
        lazyLoad: true,
        perPage: 1,
        perMove: 1,
        breakpoints: {
            767: {
                speed: 600,
            },
        },
    }).mount();


    document
        .querySelector(".product__section-info-close")
        ?.addEventListener("click", () => {
            const nodeToRemove = document.querySelector(".product__section-info");
            nodeToRemove.remove();
        });

    document.querySelectorAll(".grid-list__item-line").forEach((block) => {
        const percent = block.getAttribute("data-percent");
        block.style.background = `linear-gradient(90deg, black ${percent}%, #e3e3e3 ${percent}%)`;
    });

    let commentModal = document.querySelector('#modal-comment')

    if (commentModal) {
        let clone = commentModal.cloneNode(true)

        document.querySelector('.js-modals-list').append(clone)

        commentModal.remove()
    }

    document.querySelector('.btn-comment').addEventListener('click', ()=>{
        document.querySelector('#modal-comment').modal()
    })


});
