document.addEventListener('alpine:init', () => {

    Alpine.store('smallBasket', {
        signedParamsString: '',
        sessid: '',
        productList: [],
        lastAction: 'initialLoad',
        smallBasketSelector: '.small_basket-js',
        productSelector: '.product-card-js',
        productAddBtnSelector: '.add-to-cart-js',
        counterInputSelector: '.js-counter-input',
        allSumFormated: '',
        allCount: 0,
        allSumm: '',
        currency: '',
        fullDiscountList: [],
        open: false,
        isCartPage: false,
        componentName: '',
        componentTemplate: '',
        messages: {},
        init()
        {
            const _this = this;
            const smallBasketNode = document.querySelector(this.smallBasketSelector);
            const dataParams = JSON.parse(smallBasketNode.getAttribute('data-params'));
            this.isCartPage = dataParams['isCartPage'];

            /** если мы на странице оформления */
            if (this.stop()) return;

            this.signedParamsString = dataParams['signedParamsString'];
            this.sessid = GOODAPP.message.sessid;
            this.noPhoto = dataParams['noPhoto'];
            this.componentName = dataParams['componentName'];
            this.componentTemplate = dataParams['componentTemplate'];
            this.messages = dataParams['messages'];


            if (localStorage.getItem('smallBasket'))
            {
                this.allCount = localStorage.getItem('smallBasket');
                if (!GOODAPP.message.composite) {
                    this.initialData();
                } else {
                    /** загрузка данных корзины после композита*/
                    window.addEventListener('page-init', () => {
                        this.initialData();
                    });
                }
            }

            window.addEventListener('removeFromBasket', function ($event) {
                const data = $event.detail;
                _this.removeItem(data.basketId)
            });

            /** кастомное событие изменения каунтера в корзине*/
            window.addEventListener('change-basket-item', function ($event) {
                const data = $event.detail;
                _this.changeQuantity(data.basketId, data.count)
            });
        },
        initialData()
        {
            const _this = this;
            fetchComponentD7('gcode:smallbasket.ajax', 'prepareAjax', _this.appendRequiredParams(new FormData()))
                .then(response => {
                    return JSON.parse(response)
                })
                .then(result => {
                    if (result.status === 'success'){
                        // console.log(result.data)
                        // console.log('here')
                        _this.prepareResultData(result.data);
                    }
                })
                .catch(error => {
                    console.log(error)
                })
        },
        /**
         * разбираем ответ
         */
        prepareResultData(rawData)
        {
            const _this = this;



                _this.prepareProductList(rawData.GRID.ROWS);

                _this.allSumFormated = rawData.allSum > 0 ? rawData.allSum_FORMATED : '';
                _this.allSumm = rawData.allSum > 0 ? rawData.allSum : '';
                _this.currency = rawData.CURRENCY;

                if (rawData.FULL_DISCOUNT_LIST)
                {
                    _this.fullDiscountList = rawData.FULL_DISCOUNT_LIST
                }

                /** сохраняем данные в localStorage */
                localStorage.setItem('smallBasket', this.productList.length);
                

            },
        /**
         * разбираем массив с товарами
         * @param rawData
         */
        prepareProductList(rawData)
        {
            let _productList = [];
            for (let index in rawData)
            {
                const _product = rawData[index];
                if(_product['CAN_BUY'] !== 'Y'){
                    continue;
                }
                _productList.push(_product);
            }
            this.productList = _productList;
            this.allCount = this.productList.length;
        },

        /**
         * запрос добавления товара в корзину
         * @param productId
         * @param quantity
         */
        addToBasket(productId, quantity)
        {
            const _this = this;
            if (quantity > 0)
            {
                const findProduct = this.productList.filter((e) => e.PRODUCT_ID === productId);
                if (findProduct.length !== 0)
                {
                    _this.changeQuantity(findProduct[0].ID, findProduct[0].QUANTITY + quantity);
                }
                else
                {
                    _this.lastAction = 'addAjax';
                    const formData = new FormData();
                    formData.append('PRODUCT_ID', productId);
                    formData.append('QUANTITY', quantity);

                    fetchComponentD7('gcode:smallbasket.ajax', 'prepareAjax', _this.appendRequiredParams(formData))
                        .then(response => {
                            return JSON.parse(response)
                        })
                        .then(result => {
                            _this.prepareResultData(result['BASKET_DATA']);

                            /*
                            const rows = result['BASKET_DATA']['GRID']['ROWS'];
                            for (let index in rows){
                                const _product = rows[index];
                                if (_product['PRODUCT_ID'] === +productId)
                                {
                                    Alpine.store('alerts').add({
                                        title: _this.messages['inBasket'],
                                        descr: _product['NAME'],
                                        img: _product['DISPLAY_PICTURE']
                                    })
                                }
                            }*/
                        })
                        .catch(error => {
                            console.log(error)
                        })
                }
            }
        },
        /**
         * Запрос изменения кол-ва товара в корзине
         * @param id
         * @param count
         */
        changeQuantity(id, count)
        {
            const findProduct = this.productList.filter((e) => e.ID === id);
            if (findProduct.length === 0 || findProduct[0].QUANTITY === count)
            {
                return;
            }
            findProduct[0]['PRELOADER'] = true;
            const formData = new FormData();
            formData.append(`basket[QUANTITY_${id}]`, count);
            this.recalculateAjax(formData);
        },
        /**
         * Запрос удаления товара в корзине
         * @param id
         */
        removeItem(id)
        {
            const _this = this;
            const findProduct = _this.productList.filter((e) => e.ID === id);
            if (findProduct.length === 0){
                return;
            }
            const formData = new FormData();
            formData.append(`basket[DELETE_${id}]`, 'Y');
            _this.recalculateAjax(formData);
        },
        /**
         * Запрос удаления всех товаров
         */
        removeAll()
        {
            const _this = this;
            const formData = new FormData();
            _this.productList.forEach(function (product)
            {
                formData.append(`basket[DELETE_${product.ID}]`, 'Y');
            })
            _this.recalculateAjax(formData);
        },
        /**
         * Формируем запрос в компонент
         * @param formData
         */
        recalculateAjax(formData)
        {
            const _this = this;
            _this.lastAction = 'recalculateAjax';

            fetchComponentD7('gcode:smallbasket.ajax', 'prepareAjax', _this.appendRequiredParams(formData))
                .then(response => {
                    return JSON.parse(response)
                })
                .then(result => {
                    _this.prepareResultData(result['BASKET_DATA']);
                })
                .catch(error => {
                    console.log(error)
                })
        },
        /** Добавляем в запрос служебные параметры */
        appendRequiredParams(formData)
        {
            formData.append('action', this.lastAction);
            formData.append('sessid', this.sessid);
            formData.append('componentName', this.componentName);
            formData.append('componentTemplate', this.componentTemplate);

            formData.append('signedParameters', this.signedParamsString);
            formData.append('via_ajax', 'Y');
            formData.append('site_id', 's1');
            if (this.lastAction === 'recalculateAjax')
            {
                // we use it to reload all items if applied discounts changed
                formData.append('lastAppliedDiscounts', Object.keys(this.fullDiscountList).join(','));
            }
            return formData;
        },
        /** если нужно остановить дальнейшую работу
         * например на странице оформления
         */
        stop()
        {
            return this.isCartPage;
        },
    });

    /**
     *  управление состоянием корзины
     */
    Alpine.data('dropdownBasket', (orderPage) => ({
        open: false,
        orderPage: orderPage,
        countProducts: 0,
        stop: false,
        isMob: false,
        init()
        {
            const _this = this;
            this.orderPage = orderPage;
            this.isMob = isMobile();
            this.countProducts = Alpine.store('smallBasket').productList.length;

            Alpine.effect(() =>
            {
                _this.countProducts = Alpine.store('smallBasket').productList.length;
                _this.stop = Alpine.store('smallBasket').stop();
            })
        },
        trigger: {
            ['x-ref']: 'trigger',
            ['@click']($event)
            {
                if ($event.target.classList.contains('header__main-item-title')) {
                    window.location.href = this.orderPage
                }
            },
        },
        dialogue: {
        },
    }));
})

