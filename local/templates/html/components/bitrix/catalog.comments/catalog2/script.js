;(function(window) {
if (!!window.JCCatalogSocnetsComments)
{
	return;
}

window.JCCatalogSocnetsComments = function(arParams)
{
	var i;

	this.errorCode = 0;

	this.params = {};

	this.serviceList = {
		blog: false,
		facebook: false,
		vk: false
	};
	this.settings = {
		blog: {
			ajaxUrl: '',
			ajaxParams: {},
			contID: 'bx-cat-soc-comments-blg'
		},
		facebook: {
			contID: 'bx-cat-soc-comments-fb',
			contWidthID: '',
			parentContID: 'soc_comments',
			facebookJSDK: 'facebook-jssdk',
			facebookPath: ''
		},
		vk: {}
	};

	this.services = {
		blog: {
			obBlogCont: null
		},
		facebook: {
			obFBCont: null,
			obFBContWidth: null,
			obFBParentCont: null,
			obFBjSDK: null,
			currentWidth: 0
		}
	};

	this.activeTabId = '';
	this.currentTab = -1;
	this.tabsContId = '';
	this.tabList = [];
	this.obTabList = [];

	if (typeof arParams === 'object')
	{
		this.params = arParams;
		if (!!this.params.serviceList && typeof(this.params.serviceList) === 'object')
		{
			for (i in this.serviceList)
			{
				if (this.serviceList.hasOwnProperty(i) && !!this.params.serviceList[i])
					this.serviceList[i] = true;
			}
		}
		if (this.serviceList.blog)
			this.initParams('blog');
		if (this.serviceList.facebook)
			this.initParams('facebook');

		if (typeof(this.params.tabs) === 'object')
		{
			this.activeTabId = this.params.tabs.activeTabId;
			this.tabsContId = this.params.tabs.tabsContId;
			this.tabList = this.params.tabs.tabList;
		}
	}
	else
	{
		this.errorCode = -1;
	}
	
	if (this.errorCode === 0) {
		if (!GOODAPP.message.composite) {
			this.Init();
		} else {
			/** загрузка данных корзины после композита*/
			window.addEventListener('page-init', () => {
				this.Init();
			});
		}
	}
	//this.Init(); //BX.ready(BX.proxy(this.Init, this));
};

window.JCCatalogSocnetsComments.prototype.initParams = function(id)
{
	var i;

	if (!!this.params.settings && typeof(this.params.settings) === 'object' && typeof(this.params.settings[id]) === 'object')
	{
		for (i in this.settings[id])
		{
			if (this.settings[id].hasOwnProperty(i) && !!this.params.settings[id][i])
				this.settings[id][i] = this.params.settings[id][i];
		}
	}
};

window.JCCatalogSocnetsComments.prototype.Init = function()
{
	if (!this.tabList || !Array.isArray(this.tabList) || this.tabList.length === 0) //if (!this.tabList || !BX.type.isArray(this.tabList) || this.tabList.length === 0)
	{
		this.errorCode = -1;
		return;
	}
	var i,
		strFullId;

	for (i = 0; i < this.tabList.length; i++)
	{
		strFullId = this.tabsContId + this.tabList[i];
		this.obTabList[i] = {
			id: this.tabList[i],
			tabId: strFullId,
			contId: strFullId+'_cont',
			tab: document.querySelector('#' + strFullId), //BX(strFullId),
			cont: document.querySelector('#' + strFullId+'_cont'), //BX(strFullId+'_cont')
		};
		if (!this.obTabList[i].tab || !this.obTabList[i].cont)
		{
			this.errorCode = -2;
			break;
		}
		if (this.activeTabId === this.tabList[i])
			this.currentTab = i;
		//this.obTabList[i].tab.addEventListener('onclick', this.onClick.bind(this)); //BX.bind(this.obTabList[i].tab, 'click', BX.proxy(this.onClick, this));
	}

	if (this.serviceList.blog)
	{
		this.services.blog.obBlogCont = document.querySelector('#' + this.settings.blog.contID); //BX(this.settings.blog.contID);
		if (!this.services.blog.obBlogCont)
		{
			this.serviceList.blog = false;
			this.errorCode = -16;
		}
	}
	if (this.serviceList.facebook)
	{
		this.services.facebook.obFBCont = document.querySelector('#' + this.settings.facebook.contID); //BX(this.settings.facebook.contID);
		if (!this.services.facebook.obFBCont)
		{
			this.serviceList.facebook = false;
			this.errorCode = -32;
		}
		else
		{
			this.services.facebook.obFBContWidth = this.services.facebook.obFBCont.firstChild;
		}
		this.services.facebook.obFBParentCont = document.querySelector('#' + this.settings.facebook.parentContID); //BX(this.settings.facebook.parentContID);
	}

	if (this.errorCode === 0)
	{
		//this.showActiveTab();
		if (this.serviceList.blog)
			this.loadBlog();
		// if (this.serviceList.facebook)
		// 	this.loadFB();
	}

	this.params = {};
};

window.JCCatalogSocnetsComments.prototype.loadBlog = function()
{
	var postData;

	if (this.errorCode !== 0 || !this.serviceList.blog || this.settings.blog.ajaxUrl.length === 0)
	{
		return;
	}

	postData = {...this.settings.blog.ajaxParams};
	postData.sessid = window.GOODAPP.message.sessid;


	fetch(
		this.settings.blog.ajaxUrl, 
		{
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: new URLSearchParams(postData)
		}
	).then(res => res.text()).then(text => this.loadBlogResult(text));
	
	// postData = this.settings.blog.ajaxParams;
	// postData.sessid = document.querySelector('#' + this.params.tabs.tabsContId).getAttribute('data-sessid');
	// BX.ajax({
	// 	timeout:   30,
	// 	method:   'POST',
	// 	dataType: 'html',
	// 	url:       this.settings.blog.ajaxUrl,
	// 	data:      postData,
	// 	onsuccess: this.loadBlogResult.bind(this) //BX.proxy(this.loadBlogResult, this)
	// });
};

window.JCCatalogSocnetsComments.prototype.loadBlogResult = function(result)
{
	if (result && result.length != 0){ //if (BX.type.isNotEmptyString(result)){
		this.services.blog.obBlogCont.innerHTML = result; //BX.adjust(this.services.blog.obBlogCont, { html: result })
		let rating = this.services.blog.obBlogCont.querySelector('.blog-comments').getAttribute('data-rating');
		let count = this.services.blog.obBlogCont.querySelector('.blog-comments').getAttribute('data-count');
		
		var rating_wrapper = document.querySelector('.p-tab-review__right .grid-list__item-rating');
		rating = (+rating).toFixed(1);
		rating_wrapper.querySelector('.count').innerText = rating;
	
		var stars = rating_wrapper.querySelectorAll('div.star');
		if(stars.length) {
			var subRating = Math.round(rating)
	
			for (var i = 0; i < subRating; i++) {
				stars[i].classList.add('filled');
			}

			if(i<5){
				if( ((rating*10) % 10) > 0 ){
					stars[i].classList.add('half');
				}
			}
		}
		if(count > 0){
			document.querySelector('#tab-reviews-count').innerText += '(' + count + ')';
		}
		var tabReviews = document.querySelector('a.product-main__review[href="#reviews"],.product-block__badge[href="#reviews"]');

		tabReviews.querySelector('.count,span').innerText = count;
		const tabReviewsDn = tabReviews.querySelector('.dn');
		if (tabReviewsDn)
		{
			tabReviewsDn.innerHTML = wordform(count, ['отзыв', 'отзыва', 'отзывов']);
		}

		document.querySelectorAll('[id^="blog-comment-nav-b"]').forEach(function(itemNav) {
			itemNav.addEventListener('click', function(event){
				document.querySelectorAll('[id^="blog-comment-page-"]').forEach(function(itemPage) {
					itemPage.style.display = 'none';
				});

				let page = itemNav.getAttribute('data-page');

				document.querySelector('[id="blog-comment-page-' + page + '"]').style.display = '';

				document.querySelectorAll('[id^="blog-comment-nav-b"]').forEach(function(itemNavInner) {
					itemNavInner.classList.remove('blog-comment-nav-item-sel', 'colored_theme_bg');
				});
				document.querySelector('[id="blog-comment-nav-b' + page + '"]').classList.add('blog-comment-nav-item-sel', 'colored_theme_bg');
				document.querySelector('#reviews').scrollIntoView();
				
				event.preventDefault();
			})
		})
	}

	let a=document.querySelector("#modal-comment");
	if(a){
		// let b=a.cloneNode(!0);
		// document.querySelector(".js-modals-list").append(b);
		document.querySelector('.js-modals-list').insertAdjacentElement('afterbegin', a)

		// a.remove();
		
		let intervalInitModals = setInterval(function() {
			if(typeof window.GOODAPP.initModals != 'undefined') {
				window.GOODAPP.initModals();
				clearInterval(intervalInitModals);
				document.querySelector('.btn-comment').classList.remove('btn-comment-disabled');
			}
		}, 100);

		document.querySelector('#post-button').addEventListener('click', function(event){
			submitComment();
			event.preventDefault();
		})
	}

	document.querySelectorAll('[data-toggle="modal"][data-target="#modal-comment"]').forEach(function(btn) {
		btn.addEventListener('click', function(event){
			Alpine.store('formComments').onForm();
			let target = event.target;
			let parentID = target.getAttribute('data-parentId') ? target.getAttribute('data-parentId') : 0;
			let ratingContainer = document.querySelector('input[type="radio"]').closest('.form-group');
			let virtuesContainer = document.querySelector('#virtues').closest('.form-group');
			let limitationsContainer = document.querySelector('#limitations').closest('.form-group');
			if(parentID != 0) {
				ratingContainer.classList.add('hidden');
				virtuesContainer.classList.add('hidden');
				limitationsContainer.classList.add('hidden');
			} else {
				ratingContainer.classList.remove('hidden');
				virtuesContainer.classList.remove('hidden');
				limitationsContainer.classList.remove('hidden');
			}
			let modalComment = document.querySelector('#modal-comment');
			let parentIDInput = modalComment.querySelector('input[name="parentId"]')
			parentIDInput.value = parentID;
            
		})
	});

	document.querySelectorAll(".giveButton").forEach((giveButton) => {
		giveButton.addEventListener("click", (e) => {
			giveButton.classList.add("clicked");
			const data = new FormData();
			data.append("user_id", giveButton.dataset.id);
			data.append("postID", giveButton.dataset.post);
			fetch("/local/ajax/giveBonus.php", {
				method: "POST",
				body: data,
			})
				.then((res) => res.text())
				.then((text) => giveButton.remove());
		});
	});
};

function submitComment() {
	//oBlogComLHE.SaveContent();
	const button = document.querySelector("#post-button");
	button.focus();
	const obForm = document.querySelector("#form_comment");
	const editId = document.querySelector("#edit_id");
	let val;
	if (editId.value > 0) {
		val = editId.value;
		document.querySelector("#blg-comment-" + val).id = "blg-comment-" + val + "old";
	} else {
		val = document.querySelector("#parentId").value;
	}
	const id = "#new_comment_" + val;
	console.log(id);
	const errComment = document.querySelector("#err_comment_" + val);
	if (errComment) {
		errComment.innerHTML = "";
	}

	if (validateForm(obForm)) {
		prepareFormInfo(obForm);
		//obForm.submit();
		const data = new FormData(obForm);
		button.disabled = true;
		obForm.classList.add('loading-popover');

		const captcha = roast.captcha;
		captcha.form("submitComment", function (token) {
			data.append(captcha.name, token);
			fetch(obForm.getAttribute("action"), {
				method: "POST",
				body: data,
			})
				.then((res) => res.text())
				.then(function (text) {
					Alpine.store("formComments").onSuccess();
					document.querySelector(id).insertAdjacentHTML("afterEnd", text);

					button.disabled = false;
					obForm.classList.remove('loading-popover');
				});
		});
	}

}

function prepareFormInfo(obForm)
{
	var form = obForm;
	var comment = form.querySelector('#comment[name=comment]');
	var commentHidden = form.querySelector('[name=comment][type=hidden]');
	var limitations = form.querySelector('[name=limitations]');
	var virtues = form.querySelector('[name=virtues]');
	var rating = form.querySelector('[name=rating]');
	var parentId = form.getAttribute('data-parentId');
	var resultCommentText = '';
	if(virtues.value) {
		resultCommentText += '<virtues>'+virtues.value+'</virtues>'+'\n';
	}

	if(limitations.value) {
		resultCommentText += '<limitations>'+limitations.value+'</limitations>'+'\n';
	}

	if(comment.value) {
		resultCommentText += '<comment>'+comment.value+'</comment>';
	}

	if(resultCommentText) {
		commentHidden.value = resultCommentText;
	}
}

function validateForm(obForm){
	let erorrs = {};
	document.querySelectorAll('label[for] span.error').forEach(function(errorField) {
		errorField.classList.add('hidden');
	});

	var inputsRequired = obForm.querySelectorAll('input[required]');
	inputsRequired.forEach (function (field) {
		let type = field.getAttribute('type');
		console.log(field, field.checked);
		switch(type) {
			case 'text':
				if(field.value == '') {
					erorrs[field.getAttribute('name')] = 'Обязательно';
				}
				break;
			case 'checkbox':
				if(!field.checked) {
					erorrs[field.getAttribute('name')] = 'Обязательно';
				}
				break;
		}
	})
	
	let email = obForm.querySelector('input[name="user_email"]');
	if( email ) {
		let validEmail = email.value == '' || email.value.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != null;
		if( !validEmail ) {
			erorrs[email.getAttribute('name')] = 'Некорректный email';
		}
	}

	let ratings = obForm.querySelectorAll('input[type="radio"][required]');
	let ratingChecked = false || !["0", ""].includes(document.querySelector('input[name="parentId"]').value);
	ratings.forEach( function (rating) {
		if(rating.checked) ratingChecked = true;
	});
	if(!ratingChecked) {
		erorrs[ratings[0].getAttribute('name')] = 'Обязательно';
	}


	var textareaRequired = obForm.querySelectorAll('textarea[required]');
	textareaRequired.forEach (function (field) {
		if(field.value == '') {
			erorrs[field.getAttribute('name')] = 'Обязательно';
		}
	})
	
	for(let i in erorrs) {
		document.querySelector('label[for="' + i + '"] span.error').innerText = erorrs[i];
		document.querySelector('label[for="' + i + '"] span.error').classList.remove('hidden');
	}
	return Object.keys(erorrs).length === 0;
}

function wordform(number, txt) {
    var cases = [2, 0, 1, 1, 1, 2];
    return txt[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
}

// window.JCCatalogSocnetsComments.prototype.loadFB = function()
// {
// 	var width;

// 	if (this.services.facebook.obFBParentCont && this.services.facebook.obFBContWidth)
// 	{
// 		width = parseInt(this.services.facebook.obFBParentCont.offsetWidth, 10);
// 		if (!isNaN(width))
// 		{
// 			BX.adjust(this.services.facebook.obFBContWidth, { attrs: { 'data-width': (width-20) } });
// 			this.services.facebook.currentWidth = width;
// 		}

// 		if (!this.services.facebook.obFBjSDK)
// 		{
// 			this.services.facebook.obFBjSDK = true;
// 			BX.defer(BX.proxy((function(d, s, id, fbpath) {
// 				var js, fjs = d.getElementsByTagName(s)[0];
// 				if (d.getElementById(id))
// 				{
// 					return;
// 				}
// 				js = d.createElement(s); js.id = id;
// 				js.src = fbpath;
// 				fjs.parentNode.insertBefore(js, fjs);
// 			}(document, "script", this.settings.facebook.facebookJSDK, this.settings.facebook.facebookPath)), this));
// 		}
// 	}
// };

// window.JCCatalogSocnetsComments.prototype.getFBParentWidth = function()
// {
// 	var width = 0;
// 	if (!!this.services.facebook.obFBParentCont)
// 	{
// 		width = parseInt(this.services.facebook.obFBParentCont.offsetWidth, 10);
// 		if (isNaN(width))
// 			width = 0;
// 	}
// 	return width;
// };

// window.JCCatalogSocnetsComments.prototype.setFBWidth = function(width)
// {
// 	var obFrame = null,
// 		src,
// 		newSrc;

// 	if (
// 		this.serviceList.facebook &&
// 		this.services.facebook.currentWidth !== width &&
// 		width > 20 &&
// 		!!this.services.facebook.obFBContWidth
// 	)
// 	{
// 		if (!!this.services.facebook.obFBContWidth.firstChild && !!this.services.facebook.obFBContWidth.firstChild.fitrstChild)
// 		{
// 			obFrame = this.services.facebook.obFBContWidth.firstChild.fitrstChild;
// 			if (!!obFrame)
// 			{
// 				src = obFrame.getAttribute("src");
// 				newSrc = src.replace(/width=(\d+)/ig, "width="+width);
// 				BX.adjust(this.services.facebook.obFBContWidth, { attrs: { 'data-width': (width-20) } });
// 				this.services.facebook.currentWidth = width;
// 				BX.style(this.services.facebook.obFBContWidth.firstChild, 'width', width+'px');
// 				BX.adjust(obFrame, { attrs : { src: newSrc }, style: { width: width+'px' } });
// 			}
// 		}
// 	}
// };

// window.JCCatalogSocnetsComments.prototype.onResize = function()
// {
// 	if (this.serviceList.facebook)
// 		this.setFBWidth(this.getFBParentWidth());
// };

// window.JCCatalogSocnetsComments.prototype.onClick = function()
// {
// 	var target = BX.proxy_context,
// 		index = -1,
// 		i;

// 	for (i = 0; i < this.obTabList.length; i++)
// 	{
// 		if (target.id === this.obTabList[i].tabId)
// 		{
// 			index = i;
// 			break;
// 		}
// 	}
// 	if (index > -1)
// 	{
// 		if (index !== this.currentTab)
// 		{
// 			this.hideActiveTab();
// 			this.currentTab = index;
// 			this.showActiveTab();
// 		}
// 	}
// };

// window.JCCatalogSocnetsComments.prototype.hideActiveTab = function()
// {
// 	BX.removeClass(this.obTabList[this.currentTab].tab, 'active');
// 	BX.addClass(this.obTabList[this.currentTab].cont, 'tab-off');
// 	BX.addClass(this.obTabList[this.currentTab].cont, 'hidden');
// };

// window.JCCatalogSocnetsComments.prototype.showActiveTab = function()
// {
// 	BX.onCustomEvent('onAfterBXCatTabsSetActive_'+this.tabsContId,[{activeTab: this.obTabList[this.currentTab].id}]);
// 	BX.addClass(this.obTabList[this.currentTab].tab, 'active');
// 	BX.removeClass(this.obTabList[this.currentTab].cont, 'tab-off');
// 	BX.removeClass(this.obTabList[this.currentTab].cont, 'hidden');
// };
})(window);
document.addEventListener('alpine:init', () => {
	Alpine.store('formComments', {
		success: false,

		onSuccess() {
			this.success = true;
		},

		onForm() {
			this.success = false;
		},
		
	})
})