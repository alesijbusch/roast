window.addEventListener('load', function (event) {
    const options = {
			rootMargin: '100px',
			threshold: 0
		};


        const section = document.querySelector('.cart-checkout-sticky')
        const bill = document.querySelector('.cart-bill')
        const observer = new IntersectionObserver((entries, observer) => {
            if (entries[0].isIntersecting) {
                bill.classList.remove('active')

            }else{
                bill.classList.add('active')


            }
        }, options);
        observer.observe(section);


});