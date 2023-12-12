window.addEventListener('load', function () {

    let videoWrap = document.querySelectorAll('[data-video-id]');
    let tabs = document.querySelectorAll('.tabs-nav__item');

    tabs.forEach(tab => {
        tab.addEventListener('click', function (){
            let id = videoWrap[0].getAttribute('data-video-id');
            videoWrap[0].innerHTML = `<iframe width="640" height="360" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" src="https://www.youtube-nocookie.com/embed/${id}?enablejsapi=1&widgetid=1&autoplay=0" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="1"></iframe>`;
        })
    });
});



