document.addEventListener('alpine:init', () => {


	Alpine.data('saleOrderAjax', (parameters = {}) => {
		let yaMap; // обьект карты не должен быть проксирован
		let yaMapPickup; // обьект карты не должен быть проксирован
		return {
			result: {},
			params: {},
			options: {},
			signedParamsString: '',
			siteId: '',
			ajaxUrl: '',
			templateFolder: '',
			action: '',
			formSelector: '',
			isLoading: false,
			productList: [],
			orderPropsList: [],
			deliveryList: [],
			deliveryGroupsList: [],
			deliveryGroupsIds: [],
			activeDelivery: {},
			storeList: [],
			paySystemList: [],
			activePaySystem: {},
			activeProfile: {},
			couponList: [],
			citiesList: [],
			streetList: [],
			yaSuggestList: [],
			showYaSuggestList: false,
			sessid: '',
			frontValidateErrors: {},
			preloaderTotal: false,
			noPhoto: '',
			location: null,
			cityData: null,
			address: false,
			addressData: {},
			addressDataHidden: {},
			timeForOnlinePay: false,
			stepenPomolaGift: false,
			userConsent: {},
			propBes: false,
			propNeperez: false,
			commentary: false,
			BUYER_STORE: 0,
			DELIVERY_ID: 0,
			PAY_SYSTEM_ID: 0,
			PERSON_TYPE: 0,
			PAY_CURRENT_ACCOUNT: false,
			validation: null,
			mask: [],
			promoCode: '',
			expand: {
				products: true,
				user: true,
				delivery: true,
				pay: true,
				address: true,
				props: true
			},
			root: false,
			isCoffeExist: false,
			mapIsReady: false,
			addressIsValid: false,
			pickup: {
				items: [],
				current: null
			},
			yaMapPickup: null,
			pickupInit : false,
			tempPoint : null,

			modalPickup: null,
			mk: false,
			async init() {
				const _this = this;
				_this.root = this.$root;
				_this.result = parameters.result || {};
				_this.params = parameters.params || {};
				_this.signedParamsString = parameters.signedParamsString || '';
				_this.siteId = parameters.siteID || '';
				_this.ajaxUrl = parameters.ajaxUrl || '';
				_this.templateFolder = parameters.templateFolder || '';
				_this.action = parameters.action || '';
				_this.formSelector = parameters.formSelector || '';
				_this.noPhoto = parameters.noPhoto || '';
				_this.getSessid();
				_this.modalPickup = _this.$refs.modalPickup
				window.addEventListener('load', () => {
					try {
						setTimeout(() => {
							_this.modalPickup = _this.$refs.modalPickup
							window.GOODAPP.initModals()

							_this.modalPickup.addEventListener('GoodAppModal.opened', () => {
								this.createPickupMap()
							})
							_this.modalPickup.addEventListener('GoodAppModal.closed', () => {
								this.tempPoint = this.pickup.current
								const geo = [+this.pickup.current.PRM.Latitude, +this.pickup.current.PRM.Longitude]
								yaMapPickup?.destroy()
								yaMapPickup = null
							})

						})
					} catch (e) {

					}
				})


				/** прокидываем значения в smallBasket */
				_this.$watch('productList', () => {
					Alpine.store('smallBasket').allCount = _this.productList.length;
					Alpine.store('smallBasket').allSumFormated = _this.result.TOTAL.ORDER_PRICE_FORMATED;
				});

				/**кастомное событие изменения каунтера в корзине*/
				window.addEventListener('change-basket-item', function ($event) {
					const data = $event.detail;
					_this.changeQuantity(data.basketId, data.count)
				});

				_this.prepareResult();

				_this.$watch('PERSON_TYPE', (v) => {
					if (!Number.isInteger(v)) {
						_this.changePersonType();
					}
				});

				_this.$watch('DELIVERY_ID', (v) => {
					if (!Number.isInteger(v)) {
						_this.changeDelivery()
					}
				});

				_this.$watch('PAY_SYSTEM_ID', (v) => {
					if (!Number.isInteger(v)) {
						_this.changePaySystem();
					}
				});

				_this.$watch('address', (v) => {

					if (v !== null && typeof v !== 'undefined') {
						_this.createMap();
					}
				});

				_this.$watch('location', _this.refreshOrderAjax.bind(_this));
				_this.$watch('PAY_CURRENT_ACCOUNT', _this.refreshOrderAjax.bind(_this));

				_this.$watch('pickup.current', (v) => {

					if (!v) return
					const geo = [+v.PRM.Latitude, +v.PRM.Longitude]
					_this.changePickupProperty()
				})
				_this.$watch('pickup', (v) => {

				})

				_this.$watch('deliveryGroupsIds', (v) => {

					if (this.pickup?.items?.length && !this.pickup?.current) {
						_this.pickup.current = _this.pickup.items[0];
						_this.tempPoint = _this.pickup.items[0];
					}
					_this.changePickupProperty()
				})

				_this.initDeliveryMap(_this.BUYER_STORE, true);
				// перерисовка карты
				// _this.$watch('BUYER_STORE', (v) => {
				//     _this.initDeliveryMap(v);
				// });

				_this.completeOrderListener();
			},
			/**
			 * разбираем полученные данные от компонента
			 */
			prepareResult() {
				const _this = this;
				_this.BUYER_STORE = _this.result['BUYER_STORE'];
				console.log('result')
				console.log(_this.result)

				if (_this.result.GRID) {
					_this.prepareProductList(_this.result.GRID.ROWS);
				}

				if (_this.result.PERSON_TYPE) {
					_this.preparePersonTypeList(_this.result.PERSON_TYPE);
				}

				if (_this.result.USER_PROFILES) {
					_this.prepareProfileList(_this.result.USER_PROFILES);
				}

				if (_this.result.ORDER_PROP) {
					_this.prepareOrderProps(_this.result.ORDER_PROP);
				}

				if (_this.result.DELIVERY) {
					_this.prepareDeliveryList(_this.result.DELIVERY);
				}

				if (_this.result.DELIVERY) {
					_this.prepareDeliveryGroupList(_this.result.DELIVERY_GROUPS);
				}

				if (_this.result.STORE_LIST) {
					_this.prepareStoreList(_this.result.STORE_LIST);
				}

				if (_this.result.PAY_SYSTEM) {
					_this.preparePaySystemList(_this.result.PAY_SYSTEM);
				}

				if (_this.result.COUPON_LIST) {
					_this.prepareCouponList(_this.result.COUPON_LIST);
				}

				if (_this.result.TOTAL) {
					const total = _this.result.TOTAL;
					_this.options.showOrderWeight = total.ORDER_WEIGHT && parseFloat(total.ORDER_WEIGHT) > 0;
					_this.options.showPriceWithoutDiscount = parseFloat(total.ORDER_PRICE) < parseFloat(total.PRICE_WITHOUT_DISCOUNT_VALUE);
					_this.options.showDiscountPrice = total.DISCOUNT_PRICE && parseFloat(total.DISCOUNT_PRICE) > 0;
					_this.options.showTaxList = total.TAX_LIST && total.TAX_LIST.length;
					_this.options.showPayedFromInnerBudget = total.PAYED_FROM_ACCOUNT_FORMATED && total.PAYED_FROM_ACCOUNT_FORMATED.length;
				}
				this.initMask();

			},
			initMask() {
				Alpine.store('vendor').load('imask').then(() => {

					this.mask.forEach(e => {
						e.destroy();
					})

					this.root.querySelectorAll(['input[type=tel]']).forEach(item => {
						this.mask.push(
							IMask(item, {
								mask: GOODAPP.message.phoneMask,
								lazy: false,
								placeholderChar: '_',
								clearMaskOnLostFocus: false
							})
						);
					})
				});
			},
			/**
			 * разбор и модификация типов плательщика
			 * @param rawData
			 */
			prepareStoreList(rawData) {
				const _this = this;
				if (_this.result['BUYER_STORE'] !== 0) {
					for (let typeIndex in rawData) {
						let item = rawData[typeIndex];
						item['ID'] = Number(item['ID']);
						if (item['ID'] === _this.result['BUYER_STORE']) {
							item['CHECKED'] = true;
							continue;
						}
						item['CHECKED'] = false;
					}
				}
				_this.storeList = rawData;
			},
			/**
			 * инициализация карты складов
			 */
			initDeliveryMap(bayerStoreId, firstInit = false) {
			},
			/**
			 * разбор и модификация типов плательщика
			 * @param rawData
			 */
			preparePersonTypeList(rawData) {
				const _this = this;
				for (let typeIndex in rawData) {
					rawData[typeIndex]['ID'] = Number(rawData[typeIndex]['ID'])
					if (rawData[typeIndex]['CHECKED'] === 'Y') {
						_this.PERSON_TYPE = rawData[typeIndex]['ID'];
						break
					}
				}
			},
			/**
			 * разбор и модификация профилей
			 * @param rawData
			 */
			prepareProfileList(rawData) {
				const _this = this;
				for (let typeIndex in rawData) {
					if (rawData[typeIndex]['CHECKED'] === 'Y') {
						_this.activeProfile = rawData[typeIndex];
						break
					}
				}
			},
			/**
			 * разбор и модификация платежек
			 * @param rawData
			 */
			preparePaySystemList(rawData) {
				const _this = this;
				for (let typeIndex in rawData) {
					if (rawData[typeIndex]['CHECKED'] === 'Y') {
						let item = rawData[typeIndex];
						item['ID'] = Number(item['ID']);
						item['PAY_SYSTEM_ID'] = Number(item['PAY_SYSTEM_ID']);
						_this.activePaySystem = item;
						_this.PAY_SYSTEM_ID = _this.activePaySystem.ID;
						break
					}
				}
				_this.paySystemList = rawData;
			},
			/**
			 * разбор и модификация доставок
			 * @param rawData
			 */
			prepareDeliveryList(rawData) {
				const _this = this;
				const _deliveryList = [];
				for (const [key, delivery] of Object.entries(rawData)) {
					delivery.ID = Number(delivery.ID);

					if(delivery['CALCULATE_ERRORS'] && _this.DELIVERY_ID == delivery['ID']){
						_this.DELIVERY_ID = 0;
						_this.activeDelivery = null;
					}
					else if (delivery['CHECKED'] === 'Y' && _this.activeDelivery['ID'] !== delivery['ID']) {
						_this.activeDelivery = delivery;
						_this.DELIVERY_ID = delivery.ID;

						if (_this.result['TOTAL']['DELIVERY_PRICE'] === 0) {
							delivery.PRICE_FORMATED = 'Бесплатно';
						}
					}
					_deliveryList.push(delivery);
				}

				_this.deliveryList = _deliveryList;
			},
			/**
			 * разбор и модификация групп доставок
			 * @param rawData
			 */
			prepareDeliveryGroupList(rawData) {
				const _this = this;
				const _deliveryList = [];
				const _deliveryIds = [];
				for (const [key, delivery] of Object.entries(rawData)) {
					delivery.ID = Number(delivery.ID);
					delivery["values"] = [];

					_this.deliveryList.forEach((item) => {
						if (delivery["items"].includes(+item["ID"])) {
							delivery["values"].push({
								id: item["ID"],
								name: item["NAME"],
							});
						}
					});
					const oldDelivery = _this.deliveryGroupsList.find((e) => e.ID == delivery.ID);
					let activeId =
						(delivery["ACTIVE_ID"] && +delivery["ACTIVE_ID"]) ||
						(oldDelivery && oldDelivery.current.ID) ||
						+delivery["items"][0];
					delivery["current"] = _this.deliveryList.find((e) => e.ID === activeId);

					_deliveryList.push(delivery);
					_deliveryIds.push(delivery.ID);
				}
				_this.deliveryGroupsList = _deliveryList;
				_this.deliveryGroupsIds = _deliveryIds;
			},
			changeDeliveryGroupValue(group, val) {
				const _this = this;
				const form = document.querySelector(this.formSelector);
				let findActive = _this.deliveryList.filter(e => e.ID === val.id);
				let active = findActive.length ? findActive[0] : null;
				if (active) {
					group['current'] = active;
					_this.DELIVERY_ID = active['ID'];
					form.querySelector('#DELIVERY_ID_' + group.ID).value = active['ID'];
					//form.querySelector('#DELIVERY_ID_' + group.ID).checked = true

					setTimeout(() => {
						_this.changeDelivery()
					}, 100)

				}
			},
			openModalPickup() {
				this.modalPickup.modal()
			},

			changePickupProperty() {
				const _this = this;

				if (!_this.pickup.current?.PRM?.WarehouseId) return
				if (document.querySelector('#PICKUP_POINT') != null &&
					document.querySelector('#PICKUP_POINT').value !== _this.pickup.current?.PRM?.WarehouseId) {
					if (_this.pickup.current) {
						document.querySelector('#PICKUP_POINT').value = _this.pickup.current?.PRM?.WarehouseId;
					} else {
						document.querySelector('#PICKUP_POINT').value = '';
					}
					_this.action = 'refreshOrderAjax';
					_this.sendRequest();
				}
			},
			/**
			 * разбор и модификация купонов
			 * @param rawData
			 */
			prepareCouponList(rawData) {
				const _this = this;
				_this.couponList = rawData;
			},
			/**
			 * разбор и модификация свойств заказа
			 * свойства разбивает на группы в соответствии с настройками в админке
			 * @param rawData
			 */
			prepareOrderProps(rawData) {
				const _this = this;
				const _propsList = [];
				let _address = false;
				let _propBes = false;
				let _propNeperez = false;
				let _commentary = false;
				let _userConsent = {};
				let _addressData = {};
				let _addressDataHidden = {};
				let _timeForOnlinePay = false;
				let _stepenPomolaGift = false;
				// for (let indexGroup in rawData['groups']) {
				// const group = rawData['groups'][indexGroup];
				// group.ID = Number(group.ID);
				// group.PROPS = [];
				for (let indexProp in rawData['properties']) {
					const prop = rawData['properties'][indexProp];
					prop['PROPS_GROUP_ID'] = Number(prop['PROPS_GROUP_ID']);
					prop['ID'] = Number(prop['ID']);
					/** Обработка персональных данных будет отдельно */

					switch (prop['CODE']) {
						case 'USER_CONSENT':
							_userConsent = prop;
							continue;

						case 'BES':
							_propBes = prop;
							continue;

						case 'NEPEREZ':
							_propNeperez = prop;
							continue;

						case 'COMMENTARY':
							_commentary = prop;
							continue;

						case 'ADDRESS':
							if (_this.isFizPersonalType()) {
								_address = prop;
								continue;
							}
							break;

						case 'COORDINATES':
						case 'STREET':
						case 'HOUSE':
							if (_this.isFizPersonalType()) {
								_addressDataHidden[prop['CODE']] = prop;
								continue;
							}
							break;
						case 'ZIP':
						case 'FRAME':
						case 'APARTMENT':
							if (_this.isFizPersonalType()) {
								_addressData[prop['CODE']] = prop;
								continue;
							}
							break;
						case 'TIME_FOR_ONLINE_PAY':
							_timeForOnlinePay = prop;
							continue;

						case 'STEPEN_POMOLA_GIFT':
							let gift = _this.productList
								.find(product => product.PROPS.find(prop => prop.CODE == 'IS_GIFT' && prop.VALUE == 'Y'));

							if (gift && gift.PROPS) {
								prop.VALUE = [
									gift['PROPS'].find(prop =>
										prop.CODE == 'STEPEN_POMOLA'
									)?.VALUE
								];
							}
							_stepenPomolaGift = prop;
							continue;

						case 'PD_LOCATION':

							if (prop['DISPLAY_VALUE'] && prop['DISPLAY_VALUE']['title']) {
								_this.cityData = prop['DISPLAY_VALUE'];
							}

							break
					}

					/** Добавим тип инпута */
					prop['INPUT_TYPE'] = 'text';
					if (prop['IS_PHONE'] === 'Y') {
						prop['INPUT_TYPE'] = 'tel'

					}
					/** Добавим маску из настроек компонента */
					if (_this.params[prop['CODE'] + '_MASK']) {
						prop['MASK'] = _this.params[prop['CODE'] + '_MASK'];
					}

					_propsList.push(prop);
				}

				_this.userConsent = _userConsent;
				_this.propBes = _propBes;
				_this.propNeperez = _propNeperez;
				_this.commentary = _commentary;
				_this.address = _address;
				_this.addressData = _addressData;
				_this.addressDataHidden = _addressDataHidden;
				_this.timeForOnlinePay = _timeForOnlinePay;
				_this.stepenPomolaGift = _stepenPomolaGift;
				_this.orderPropsList = _propsList.sort((a, b) => a.SORT - b.SORT);


				// if (_this.cityData) {
				//     _this.$nextTick(function () {
				//         _this.changeLocation(_this.cityData);
				//     });
				// }

				setTimeout(() => {
					if (_this.addressDataHidden['COORDINATES'] && _this.addressDataHidden['COORDINATES']['VALUE'][0] !== '') {
						_this.changeMap(_this.addressDataHidden['COORDINATES']['VALUE'][0].split(' '));
					}

					this.validateAddress();
					this.revalidateAddress();
				}, 1000);
			},
			isFizPersonalType() {
				const _this = this;
				return _this.PERSON_TYPE && _this.result.PERSON_TYPE && _this.result.PERSON_TYPE[_this.PERSON_TYPE].NAME == 'Физическое лицо';
			},
			/**
			 * разбор и модификация списка товаров
			 * @param rawData
			 */
			prepareProductList(rawData) {
				const _this = this;
				const _productList = [];
				_this.isCoffeExist = false;

				for (let index in rawData) {
					const _product = rawData[index].id ? rawData[index]['data'] : rawData[index];
					_product['ID'] = Number(_product['ID'])
					_product['PRODUCT_ID'] = Number(_product['PRODUCT_ID']);
					_product['QUANTITY'] = Number(_product['QUANTITY']);
					_product['AVAILABLE_QUANTITY'] = Number(_product['AVAILABLE_QUANTITY']);
					_product['PRELOADER'] = false;
					_product['MAX'] = _product['QUANTITY'] === _product['AVAILABLE_QUANTITY'];
					_product['MSG_COUNTER'] = _product['MEASURE_NAME'] + ' на сумму ' + _product['SUM'];
					if (_product['MAX']) {
						_product['MSG_COUNTER'] += ' (максимально)'
					}
					_product['DISPLAY_PICTURE'] = _product['PREVIEW_PICTURE_SRC'] ? _product['PREVIEW_PICTURE_SRC'] : _product['DETAIL_PICTURE_SRC'];
					if (_product['DISPLAY_PICTURE'].length === 0) {
						_product['DISPLAY_PICTURE'] = _this.noPhoto
					}
					if (!_this.isCoffeExist) {
						_this.isCoffeExist = !!_product.PROPERTY_STEPEN_POMOLA_VALUE;
					}
					if(Number(_product['PRODUCT_ID']) === 28746){
						console.log('mk')
						_this.mk = true
					}

					_product['IS_POMOL'] = false;
					_product['PROPS'].forEach((prop, index) => {
						if (prop['CODE'] === 'PARENT_ID') {
							_product['IS_POMOL'] = true;
							_product['AVAILABLE_QUANTITY'] = 9999;
						}
					})

					_productList.push(_product);
				}
				_this.productList = _productList;
			},

			/**
			 * Действия при смене PERSON_TYPE
			 */
			changePersonType() {
				this.refreshOrderAjax();
			},
			/**
			 * Действия при смене доставки
			 */
			changeDelivery() {
				this.refreshOrderAjax();

				if (this.addressDataHidden['COORDINATES'] && this.addressDataHidden['COORDINATES']['VALUE'][0] !== '') {
					this.changeMap(this.addressDataHidden['COORDINATES']['VALUE'][0].split(' '));
				}
			},
			/**
			 * Действия при смене платежки
			 */
			changePaySystem() {
				this.refreshOrderAjax();
			},
			/**
			 * Запрос изменения кол-ва товара в корзине
			 * @param id
			 * @param count
			 */
			changeQuantity(id, count) {
				const _this = this;
				_this.action = 'basketItems';
				const findProduct = _this.productList.filter((e) => e.ID === Number(id));
				if (findProduct.length !== 0 && findProduct[0].QUANTITY !== count) {
					findProduct[0]['PRELOADER'] = true;
					_this.sendRequest({
						products: [
							{
								'id': id,
								'val': count,
								'action': 'QUANTITY_' + id
							}
						]
					});
				}
			},
			/**
			 * Запрос удаления товара в корзине
			 * @param id
			 */
			removeItem(id) {
				const _this = this;
				_this.action = 'basketItems';

				// Добавляем прелоудер на удаляемый товар
				let prodIndex = false;
				if (_this.productList.length > 0) {
					_this.productList.forEach(function (value, key) {
						if (value.ID === id) {
							prodIndex = key;
						}
					});
				}

				if (prodIndex !== false) {
					_this.productList[prodIndex].PRELOADER = true;
				}

				_this.sendRequest({
					products: [
						{
							'id': id,
							'val': 'Y',
							'action': 'DELETE_' + id
						}
					]
				});

				const dataEcommerce = {
					currencyCode: _this.productList[prodIndex].CURRENCY,
					products: [{
						id: _this.productList[prodIndex].PRODUCT_ID,
						name: _this.productList[prodIndex].NAME,
					}]
				};

				sendEcommerce('remove', JSON.stringify(dataEcommerce));
			},

			/**
			 * Запрос удаления всех товаров в корзине
			 */
			removeAll() {
				const _this = this;
				_this.action = 'basketItems';

				let customParams = [];

				_this.productList.forEach(function (value) {
					customParams.push({
						'id': value.ID,
						'val': 'Y',
						'action': 'DELETE_' + value.ID,
					})
				});

				_this.preloaderTotal = true;

				_this.sendRequest({
					products: customParams,
				});
			},

			/**
			 * Ввод нового купон
			 */
			enterCoupon() {
				const _this = this,
					couponNode = _this.$refs.coupon,
					coupon = couponNode.value
				if (coupon.length === 0) {
					// TODO добавить валидацию воля ввода купона
					couponNode.classList.add('validate-error')
					return
				}

				if (_this.couponList.filter((e) => e.COUPON === coupon).length) {
					// todo показать уведомление о том что такой купон уже добавлен
					couponNode.classList.add('validate-error')
					return;
				}
				couponNode.classList.remove('validate-error')
				_this.action = 'enterCoupon';
				_this.sendRequest({
					coupon: coupon
				});
				//  очистить input
				_this.$refs.coupon.value = '';
				_this.promoCode = ''
			},
			/**
			 * Удаление купона
			 * @param coupon
			 */
			editCoupon(coupon) {
				const _this = this;
				_this.action = 'removeCoupon';
				if (coupon.length) {
					_this.sendRequest({
						coupon: coupon
					});
				}
				_this.promoCode = ''
			},
			/**
			 * Добавление купона в отрисованные
			 * @param result
			 */
			addCoupon(result) {
				this.couponList.push({
					'COUPON': result,
					'JS_STATUS': 'BAD',
					'JS_CHECK_CODE': 'не найден'
				})
			},
			/**
			 * удаление купона из отрисованных
			 * @param result
			 */
			removeVisibleCoupon(result) {
				const _this = this;
				_this.couponList.forEach((item, index) => {
					if (item.COUPON === result) {
						_this.couponList.splice(index, 1)
					}
				});
			},
			/**
			 * обновить данные
			 */
			refreshOrderAjax() {
				const _this = this;
				_this.action = 'refreshOrderAjax';
				_this.sendRequest();
			},
			/**
			 * изменение значения свойства корзины
			 */
			changeBasketProperty(id, value, code) {
				const _this = this;
				_this.action = 'changeBasketProperty';
				document.querySelector('#STEPEN_POMOLA_GIFT').value = value;
				document.querySelector('.offer-item-title-js span').setAttribute('x-text', `"${value}"`);
				_this.sendRequest({
					basketProperty: {
						'id': id,
						'value': value,
						'code': code
					}
				});
			},
			/**
			 * инициализация финального шага
			 */
			dispatchCompleteOrder() {
				const form = document.querySelector(this.formSelector);
				const event = new Event('submit', {
					'bubbles': true,
					'cancelable': true
				});
				form.dispatchEvent(event);
			},
			/** Событие комплита формы */
			async completeOrderListener() {
				const _this = this;
				// валидация
				await this.initValidator();

				this.validation.onSuccess((e) => {
					e.preventDefault();
					_this.action = 'saveOrderAjax';
					_this.sendRequest();
				});
			},
			validateAddress() {
				if (!this.isFizPersonalType()) {
					this.addressIsValid = true;
					return;
				}

				this.addressIsValid = false;
				if (!this.address || !this.cityData) {
					return;
				}

				let city = this.cityData.name.toLowerCase();
				// let value = this.address['VALUE'][0].toLowerCase();
				let value = document.getElementById('ADDRESS').value.toLowerCase();
				city = city.split(' ')[0];

				// проверка, входит ли выбранный город в адрес
				if (value !== '' && value.includes(city)) {
					this.addressIsValid = true;
					return;
				}
				this.addressIsValid = false;
			},
			revalidateAddress() {
				this.validation.revalidateField('#ADDRESS');
			},
			async initValidator() {
				if (!this.validation) {
					await Alpine.store('vendor').load('validator');
				} else {
					this.validation.destroy();
				}

				this.validateAddress();

				const form = document.querySelector(this.formSelector);
				const properties = this.result['ORDER_PROP']['properties'];
				const regexp = GOODAPP.message.regexp;

				this.validation = new JustValidate(form, {
					errorFieldCssClass: 'is-invalid',
					errorFieldStyle: {
						border: '1px solid red',
					},
					validateBeforeSubmitting: true,
					focusInvalidField: true,
					lockForm: true,
				});

				this.validation.onFail(fields => {
					const invalidField = document.querySelector('.just-validate-error-label').closest('.form-group');

					function handleButtonClick(el) {
						el.scrollIntoView({block: "center", inline: "start"});
					}

					if (document.querySelectorAll('.just-validate-error-label').length > 0) {
						handleButtonClick(invalidField);
					}
				});

				properties.forEach(field => {
					const rules = [];
					if (field['CODE'] === 'USER_CONSENT') {
						rules.push({
							rule: 'required',
							errorMessage: 'Обязательное поле'
						})
					}

					if (field['REQUIRED'] === 'Y') {
						rules.push({
							rule: 'required',
							errorMessage: field['NAME'] + ' обязательное поле',
						})
					}

					if (field['CODE'] === 'PHONE') {
						rules.push({
							rule: 'customRegexp',
							value: regexp.phone,
							errorMessage: 'Введите валидный номер'
						})
					}

					if (field['CODE'] === 'ZIP') {
						rules.push({
							validator: (value) => {
								return /^\d{6}$/.test(value);
							},
							errorMessage: 'Введите валидный индекс'
						})
					}

					if (field['CODE'] === 'ADDRESS') {
						rules.push({
							validator: (value) => {
								return this.addressIsValid;
							},
							errorMessage: 'Адрес не входит в населенный пункт который вы выбрали'
						})

						rules.push({
							validator: (value) => {
								const arAddress = value.split(',');
								return arAddress.length > 2;
							},
							errorMessage: 'Адрес не полный'
						})
					}

					if (field['CODE'] === 'EMAIL') {
						rules.push({
							rule: 'customRegexp',
							value: regexp.email,
							errorMessage: 'Введите валидный email'
						})
					}

					if (field.MINLENGTH) {
						rules.push({
							rule: 'minLength',
							value: parseInt(field.MINLENGTH),
							errorMessage: `${field.NAME} состоит минимум из ${field.MINLENGTH} символов`
						});
					}
					if (field.MAXLENGTH) {
						rules.push({
							rule: 'maxLength',
							value: parseInt(field.MAXLENGTH),
							errorMessage: `${field.NAME} состоит максимум из ${field.MAXLENGTH} символов`
						});
					}

					if (rules.length) {
						this.validation
							.addField('#' + field['CODE'], rules)
					}
				});

				this.validation.addRequiredGroup("#DELIVERY_ID", "", {
					errorsContainer: "#DELIVERY_ID .alert-danger",
					errorLabelStyle: {
						display: "none",
					},
				});
				this.validation.addRequiredGroup("#PAY_SYSTEM_ID", "", {
					errorsContainer: "#PAY_SYSTEM_ID .alert-danger",
					errorLabelStyle: {
						display: "none",
					},
				});

				this.validation.validate();

			},
			/**
			 * получение сессии из input формы
			 */
			getSessid() {
				/*
				const form = document.querySelector(this.formSelector)
				this.sessid = form.querySelector('#sessid').value;
				 */
				this.sessid = GOODAPP.message.sessid
			},
			/**
			 * формирование массива данных для отправки запроса
			 * @param actionData
			 * @returns {FormData}
			 */
			getData(actionData) {
				const _this = this;
				const allFormData = this.getAllFormData();
				// добавляем обязательные значения перед отправкой
				allFormData.append('sessid', _this.sessid);
				allFormData.append('via_ajax', 'Y');
				allFormData.append('SITE_ID', _this.siteId);
				allFormData.append('signedParamsString', _this.signedParamsString);
				allFormData.append(_this.params.ACTION_VARIABLE, _this.action);
				return allFormData;
			},
			/**
			 * Получение и валидация значений из формы
			 * @returns {FormData}
			 */
			getAllFormData() {
				const _this = this,
					form = document.querySelector(_this.formSelector),
					formData = new FormData(form),
					preparedData = new FormData();
				if (_this.action !== 'saveOrderAjax') {
					for (let [name, value] of formData) {
						if (value !== '') {
							preparedData.append(`order[${name}]`, value);
						}
					}
					preparedData.append(`order[PERSON_TYPE]`, _this.PERSON_TYPE);
					preparedData.append(`order[PERSON_TYPE_OLD]`, _this.PERSON_TYPE);
				} else {
					for (let [name, value] of formData) {
						if (value !== '') {
							preparedData.append(name, value);
						}
					}
					preparedData.append(`PERSON_TYPE`, _this.PERSON_TYPE);
					preparedData.append(`PERSON_TYPE_OLD`, _this.PERSON_TYPE);
				}
				// если ORDER_DESCRIPTION вне тега form
				preparedData.append(`ORDER_DESCRIPTION`, _this.result['ORDER_DESCRIPTION']);
				return preparedData;
			},
			/**
			 * Refreshes order via json data from ajax request
			 */
			refreshOrder: function (result) {
				const _this = this;
				// TODO надо будет придумать другую проверку
				if (result.order.GRID) {
					_this.result = result.order;
					_this.prepareResult();
				}
				if (Object.keys(result.order.ERROR).length !== 0) {
					console.error('внимание, есть ошибки')
					console.error(result.order.ERROR)
					// TODO метод для вывода ошибок
				}
			},
			guessCity($event) {
				const _this = this;
				const formData = new FormData();
				let name = $event.target.value;

				if (name.length < 3) {
					this.location = '';
					this.$refs.locationReal.value = '';
					return;
				}
				formData.append('guess', name);
				formData.append('sessid', GOODAPP.message.sessid);

				fetch('/location/', {
					method: 'POST',
					body: formData
				}).then(res => res.json())
					.then(result => {
						_this.citiesList = result['data']
					})
			},
			prepareSuggestAddressString($event) {
				let searchStr = this.cityData.name + ' ' + $event.target.value;
				// нижний регистр
				searchStr = searchStr.toLowerCase();
				// удаляем запятые
				searchStr = searchStr.replace(/,/g, '');

				// удаляем не нужные слова
				const unwantedWords = ['агрогородок', 'деревня', 'поселок', 'посёлок'];
				let wordsArray = searchStr.split(' ');
				wordsArray = wordsArray.filter(word => !unwantedWords.includes(word));
				searchStr = wordsArray.join(' ');

				return "Беларусь " + searchStr;
			},
			guessAddress($event) {
				const _this = this;

				if (_this.cityData && $event.target.value.length > 3) {

					const options = {
						types: 'house',
						results: 10,
						print_address: 1
					};

					ymaps.suggest(_this.prepareSuggestAddressString($event), options)
						.then(function (items) {
							_this.yaSuggestList = [];

							console.log(items);
							items.forEach(function (item) {
								if (_this.validateAddressSuggest(item.displayName)) {
									_this.yaSuggestList.push(item)
								}
							});

							if (_this.yaSuggestList.length) {
								_this.showYaSuggestList = true;
							}
						});
				}

				this.validateAddress();
				this.revalidateAddress();
			},
			changeAddress($event, suggest) {
				this.showYaSuggestList = false;
				const formData = new FormData();

				formData.append('suggest', suggest.displayName);
				formData.append('sessid', GOODAPP.message.sessid);

				fetch('/address/', {
					method: 'POST',
					body: formData
				}).then(res => res.json())
					.then(result => {
						if (result.status === 'success') {
							this.address['VALUE'][0] = suggest.displayName;
							document.getElementById(this.address['CODE']).value = suggest.displayName;


							if (this.addressData['ZIP']) {
								this.addressData['ZIP']['VALUE'][0] = result.data['zip'];
								document.getElementById(this.addressData['ZIP']['CODE']).value = result.data['zip'];
							}

							if (this.addressDataHidden['COORDINATES']) {
								this.addressDataHidden['COORDINATES']['VALUE'][0] = result.data['position'].join(' ');
							}
							if (this.addressDataHidden['HOUSE']) {
								this.addressDataHidden['HOUSE']['VALUE'][0] = result.data['house'];
							}

							if (this.addressDataHidden['STREET']) {
								this.addressDataHidden['STREET']['VALUE'][0] = result.data['street'];
							}

							this.addressIsValid = true;
							this.revalidateAddress();
							this.changeMap(result.data.position);

							this.action = 'refreshOrderAjax';
							this.sendRequest(this.addressDataHidden);
						}
					})
					.catch(function (error) {
						console.log(error);
					})
			},
			changeLocation(city) {
				if (city && city.code) {
					if (yaMapPickup) {
						yaMapPickup?.destroy()
						yaMapPickup = null
					}
					if (document.querySelector('.location-selector')) {
						document.querySelector('.location-selector').value = city.title;
					}
					if (document.querySelector('.location-hidden')) {
						document.querySelector('.location-hidden').value = city.code;
					}

					this.location = city.code;
					this.cityData = city;


					this.validateAddress();
					this.revalidateAddress();

					if (city.code !== this.pickup.location) {
						this.getPickupPoints(city.code);
					}
				}
			},
			getPickupPoints(cityCode) {
				const formData = new FormData();
				formData.append('location', cityCode);
				formData.append('sessid', GOODAPP.message.sessid);
				fetch('/pickup/', {
					method: 'POST',
					body: formData
				}).then(res => res.json())
					.then(result => {
						if (result.status === 'success') {
							this.pickup.items = result.data.list;
							this.pickup.location = cityCode;
							this.pickup.current = null;

							this.changePickupProperty()
						}
					})
					.catch(function (error) {
						console.log(error);
					})
			},
			addLocationToSearchInput($event) {
			},
			validateAddressSuggest(input) {
				// Разбиваем строку по запятой
				const parts = input.split(',');

				// Проверяем, что второй элемент существует
				if (parts.length < 2) {
					return false;
				}

				const secondElement = parts[1].trim(); // Убираем пробелы по краям

				// Проверяем, что длина не более 4 символов и начинается с числа
				return /^\d/.test(secondElement) && secondElement.length <= 5;
			},
			createMap() {
				if (yaMap || this.isFizPersonalType() === false) {
					return;
				}

				ymaps.ready(function () {
					yaMap = new ymaps.Map("delivery_map", {
						center: [53.9006, 27.5590],
						zoom: 12,
						controls: []
					});

					yaMap.behaviors.disable(['drag']);
				})
			},
			changeMap(coordsList) {
				if (!yaMap || this.addressIsValid === false || this.isFizPersonalType() === false) {
					return;
				}

				const mapNode = document.getElementById('delivery_map');

				if (mapNode && yaMap === null){
					this.createMap();
				}

				this.$nextTick(()=>{
					yaMap.geoObjects.removeAll();
					yaMap.setCenter(coordsList, 14);

					// Добавляем метку на карту
					yaMap.geoObjects.add(new ymaps.Placemark(
						coordsList, // Координаты метки
						{},
						{
							preset: 'islands#icon',
							iconColor: '#0095b6'
						}
					));
				})

			},

			clickPoint(v,index){
				const _this = this
				const geo = [+v.PRM.Latitude, +v.PRM.Longitude]
				document.dispatchEvent(new CustomEvent('mapCenterChange', {
					detail: {
						index,
						geo
					}
				}))

				this.tempPoint = v

			},

			createPickupMap() {
				const _this = this
				function init() {
					// Инициализация ObjectManager с кластеризацией
					const objectManager = new ymaps.ObjectManager({
						clusterize: true,
						gridSize: 64,
						clusterDisableClickZoom: false
					});

					// Создание карты
					yaMapPickup = new ymaps.Map('pickup-map', {
						center: [+_this.pickup.current.PRM.Latitude, +_this.pickup.current.PRM.Longitude],
						zoom: 14,
						controls: []
					});

					// Добавление менеджера объектов на карту
					yaMapPickup.geoObjects.add(objectManager);

					// Настройка стилей кластеров
					objectManager.clusters.options.set({
						preset: 'islands#invertedVioletClusterIcons',
						clusterIconColor: '#7351a8'
					});

					// Подготовка данных для меток
					const features = _this.pickup.items.map((item, index) => ({
						type: "Feature",
						id: index,
						geometry: {
							type: "Point",
							coordinates: [+item.PRM.Latitude, +item.PRM.Longitude]
						},
						properties: {
							hintContent: item.PRM.WarehouseName,
							isCurrent: item === _this.pickup.current
						},
						options: {
							// Используем стандартные иконки Яндекса
							preset: item === _this.pickup.current
								? 'islands#redIcon'
								: 'islands#blueIcon',
							// Дополнительные параметры иконки
							iconColor: item === _this.pickup.current ? '#ff0000' : '#1e98ff',
							iconShape: {
								type: 'Circle',
								coordinates: [0, 0],
								radius: 20
							}
						}
					}));

					// Добавление всех объектов
					objectManager.add({
						type: "FeatureCollection",
						features: features
					});

					// Обработчик клика на кластер
					objectManager.clusters.events.add('click', function(e) {
						const cluster = e.get('target');
						const clusterCenter = cluster.geometry.getCoordinates();
						const clusterPoints = cluster.properties.geoObjects.length;
						const currentZoom = yaMapPickup.getZoom();

						// Определение уровня зума
						let targetZoom = currentZoom + Math.max(1, 4 - Math.floor(clusterPoints / 10));
						targetZoom = Math.min(targetZoom, 18);

						// Плавное перемещение и зум
						yaMapPickup.panTo(clusterCenter, {
							flying: true,
							duration: 500
						}).then(() => yaMapPickup.setZoom(targetZoom, {duration: 500}));

						e.preventDefault();
					});

					// Обработчик клика на метки
					objectManager.objects.events.add('click', function(e) {
						const objectId = e.get('objectId');
						highlightActivePlacemark(objectId);
						_this.tempPoint = _this.pickup.items[objectId];
						e.preventDefault();
					});

					// Функция подсветки активной метки
					function highlightActivePlacemark(activeId) {
						objectManager.objects.each(obj => {
							objectManager.objects.setObjectOptions(obj.id, {
								preset: obj.id === activeId ? 'islands#redIcon' : 'islands#blueIcon',
								iconColor: obj.id === activeId ? '#ff0000' : '#1e98ff'
							});
						});
					}

					// Обработчик кастомного события
					document.addEventListener('mapCenterChange', ({detail: {geo, index}}) => {
						highlightActivePlacemark(index);
						yaMapPickup.setCenter(geo, 16, {checkZoomRange: true});
					});
				}

				if(!this.pickupInit){
					ymaps.ready(init);
				} else {
					init()
				}

			},

			submitPoint(){
				this.pickup.current = this.tempPoint
				this.modalPickup.modal('hide')
			},

			/**
			 * Инициализация и обработка запросов в компонент
			 * @param customParams
			 */
			async sendRequest(customParams = {}) {
				const _this = this;
				const formData = _this.getData();
				/** подмешиваем данные товаров */
				if (customParams.products && customParams.products.length) {
					customParams.products.forEach(function (product) {
						formData.append(`order[${product.action}]`, product.val);
					})
				}
				/** подмешиваем купон */
				if (_this.action === 'enterCoupon' || _this.action === 'removeCoupon') {
					formData.append('coupon', customParams.coupon);
				}

				/** подмешиваем значение свойства корзины */
				if (customParams.basketProperty) {
					formData.append(`order[basket_property][id]`, customParams.basketProperty.id);
					formData.append(`order[basket_property][value]`, customParams.basketProperty.value);
					formData.append(`order[basket_property][code]`, customParams.basketProperty.code);
				}

				/** скрытые поля с адресом */
				if (customParams.STREET) {
					formData.append(`order[ORDER_PROP_${customParams.STREET.ID}]`, customParams.STREET.VALUE);
				}
				if (customParams.HOUSE) {
					formData.append(`order[ORDER_PROP_${customParams.HOUSE.ID}]`, customParams.HOUSE.VALUE);
				}
				if (customParams.PICKUP_ORDER) {
					formData.append(`order[ORDER_PROP_${customParams.PICKUP_ORDER.ID}]`, customParams.PICKUP_ORDER.VALUE);
				}

				_this.preloaderTotal = true;
				await _this.fetchBasket(formData)
					.then(response => {
						return JSON.parse(response)
					})
					.then(result => {
						let redirect = null;

						if (result.redirect && result.redirect.length) {
							redirect = result.redirect;
						}
						if (result.REDIRECT_URL && result.REDIRECT_URL.length) {
							redirect = result.REDIRECT_URL;
						}
						if (result.order && result.order.REDIRECT_URL && result.order.REDIRECT_URL.length) {
							redirect = result.order.REDIRECT_URL;
						}

						// order completed
						if (redirect) {
							sendAnalyticsEvent('event', 'sendForm', {
								'formName': 'purchaseСompleted',
								'formURL': GOODAPP.message.siteName,
							});
							document.location.href = redirect;
						}

						// TODO сделать загрузку файлов
						//this.saveFiles();
						switch (_this.action) {
							case 'basketItems':
							case 'refreshOrderAjax':
								_this.refreshOrder(result);
								break;
							case 'confirmSmsCode':
							case 'showAuthForm':
								//this.firstLoad = true;
								_this.refreshOrder(result);
								break;
							case 'enterCoupon':
								if (result && result.order) {
									//this.deliveryCachedInfo = [];
									_this.refreshOrder(result);
								} else {
									_this.addCoupon(result);
								}
								break;
							case 'removeCoupon':
								if (result && result.order) {
									//this.deliveryCachedInfo = [];
									_this.refreshOrder(result);
								} else {
									_this.removeVisibleCoupon(result);
								}
								break;
						}
					})
					.catch(error => {
						console.log(error);
					})
					.finally(() => {
						_this.preloaderTotal = false;
						_this.completeOrderListener();
					});
			},
			/**
			 * Запрос ajax в компонент
			 * @param formData
			 */
			fetchBasket(formData) {
				const _this = this;
				return new Promise(function (resolve, reject) {
					const xhr = new XMLHttpRequest();
					xhr.open('POST', _this.ajaxUrl, true)
					xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
					xhr.onload = function () {
						if (this.status >= 200 && this.status < 300) {
							resolve(xhr.response);
						} else {
							reject({
								status: this.status,
								statusText: xhr.statusText
							});
						}
					};
					xhr.onerror = function () {
						reject({
							status: this.status,
							statusText: xhr.statusText
						});
					};
					if (_this.action == 'saveOrderAjax') {
						const captcha = roast.captcha;
						captcha.form('saveOrderAjax', function (token) {
							formData.append(captcha.name, token);
							xhr.send(formData);
						});
					} else {
						xhr.send(formData);
					}

				})
			},
		}
	})

});

