document.addEventListener('alpine:init', () => {

    Alpine.data('brand', () => ({
        isShow: false,
        isOpenedText: false,
        text: document.querySelector('.brand__info-content-inner'),
        init(){
            if(this.text.scrollHeight > 105){
                this.isShow = true
            }

        }
    }))})