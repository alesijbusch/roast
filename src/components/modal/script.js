(function () {
	'use strict';
	window.addEventListener("load", function () {

		!window.GOODAPP && (window.GOODAPP = {});
		window.GoodAppModal = class GoodAppModal {
			TRANSITION_TIME = 300;
			constructor(a) {
				this.element = document.getElementById(a);
				if (!this.element) console.warn('element not found');
				this.backdrop = document.createElement("div");
				!this.element || document.getElementById(a).modal || (document.getElementById(a).modal = (a, b = void 0) => "hide" === a ? b?.immediately ? this.hide(!0) : this.hide() : void this.show(), (this.element.querySelector(".modal-close"))?.addEventListener("click", () => {
					this.hide()
				}), this.element.addEventListener("click", a => {
					a.target.closest(".modal-content") || this.hide()
				}))

                this.element.querySelectorAll('[data-dismiss]')?.forEach(x => {
                    x.addEventListener('click', () => {
                        this.hide();
                    })
                })


            }
			addBackdrop() {
				this.backdrop.classList.add("modal-backdrop"), this.backdrop.classList.add("fade"), document.querySelector("body").append(this.backdrop), setTimeout(() => {
					this.backdrop.classList.add("show"), this.backdrop.classList.add("in");
					this.element.dispatchEvent(new Event('GoodAppModal.opened'));
				}, 0)
			}
			removeBackdrop(a = void 0) {
				a ? this.backdrop.remove() : (this.backdrop.classList.remove("show"), this.backdrop.classList.remove("in"), setTimeout(() => {
					this.backdrop.remove()
					this.element.dispatchEvent(new Event('GoodAppModal.closed'));
				}, this.TRANSITION_TIME));
			}
			show() {
				this.element.style.display = "block", document.querySelector("body").style.paddingRight = GoodAppModal.getScrollWidth() + "px", document.querySelector("body").classList.add("modal-open"), this.element.classList.add("show"), this.element.classList.add("in"), this.addBackdrop()
			}
			hide(a = void 0) {
				a ? (this.removeBackdrop(!0), this.element.style.display = "none", document.querySelector("body").style.paddingRight = null, document.querySelector("body").classList.remove("modal-open"), this.element.classList.remove("show"), this.element.classList.remove("in")) : (this.element.classList.remove("show"), this.element.classList.remove("in"), this.removeBackdrop(), setTimeout(() => {
					this.element.style.display = "none", document.querySelector("body").style.paddingRight = null, document.querySelector("body").classList.remove("modal-open")
				}, .8 * this.TRANSITION_TIME))
			}
		}
		GoodAppModal.elements = [];
		GoodAppModal.getScrollWidth = () => {
			if (document.body.scrollHeight <= window.innerHeight) return 0;
			else {
				let a = document.createElement("div");
				a.style.overflowY = "scroll", a.style.width = "50px", a.style.height = "50px", document.body.append(a);
				let b = a.offsetWidth - a.clientWidth;
				return a.remove(), b
			}
		};

		!window.GOODAPP && (window.GOODAPP = {});

		window.GOODAPP.initModals = () => {
			[...document.querySelectorAll(".modal")].forEach(b => {
				const c = b.getAttribute("id").split("#").join("");
				if (b.modal) return;
				new GoodAppModal(c)
			});

			[...document.querySelectorAll("[data-target][data-toggle=\"modal\"]")].forEach(a => {
				const isExist = GoodAppModal.elements.find(x => x === a);
				if (isExist) return;
				GoodAppModal.elements.push(a);
				const b = a.getAttribute("data-target").split("#").join(""),
					c = document.getElementById(b);
				a.addEventListener("click", () => {
					c.modal()
				})
			});
		}

		window.GOODAPP.initModals();

	})


})();
