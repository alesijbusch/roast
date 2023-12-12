import uiInits from './init';
import lazy_img from '../../mixins/lazy-img/script';


const ready = (callback) => {
	document.readyState != "loading"
		? callback()
		: document.addEventListener("DOMContentLoaded", callback);
};

ready(() => {
	uiInits.init();
	lazy_img();

	// youtube iframe
	// .lazy data-src
	const config = {
		root: null, // Sets the framing element to the viewport
		rootMargin: "0px",
		threshold: 0
	}

	const observerCallback = (entries) => {
		entries.forEach((x) => {
			if(x.isIntersecting && !x.target.classList.contains('loaded')){
				let src = x.target.getAttribute('data-src');
				x.target.setAttribute('src',src);
				x.target.classList.add('loaded');
			}
		})
	}

	const observer = new IntersectionObserver(observerCallback, config);
	let container = document.querySelectorAll('iframe.lazy');
	if(container.length) {
		container.forEach(item => {
			observer.observe(item);
		})
	}
});

window.addEventListener('ajax-finished', ()=> {
	lazy_img();
})