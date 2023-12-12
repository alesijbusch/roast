window.addEventListener('load', function () {

    let videoWrap = document.querySelectorAll('[data-video-id]');

    videoWrap.forEach(video => {
        video.addEventListener('click', function (){
            let id = this.getAttribute('data-video-id');

            let player = document.getElementById('youtube-player-id-' + id);
            player.classList.add('active');
            player.innerHTML = `<iframe width="640" height="360" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" src="https://www.youtube-nocookie.com/embed/${id}?enablejsapi=1&widgetid=1&autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="1"></iframe>`;
        })
    });
});



