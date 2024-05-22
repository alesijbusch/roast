document.addEventListener('alpine:init', () => {
    /**
     * хранилище фильтра
     */
    Alpine.store('filter', {
        items: {}
    });

    /**
     * компонент фильтра
     */
    Alpine.data('filterApp', (params) => ({
        openFilter: false,
        initFilter: false,
        arResult: {},
        form: null,
        values: {},
        preloaderContainer: null,
        clearItems: [],
        activeItems: [],
        init() {
            this.form = this.$refs['filterForm'];
            this.preloaderContainer = document.querySelector('.ajax_container');
            this.deferInit();
        },
        deferInit(){
            this.prepareResult(JSON.parse(params));
            this.addListeners();
        },
        addListeners(){
            const _this = this;
            // отправка формы
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.reloadPage()
            })
            // событие удаления всех значений свойства
            window.addEventListener('filter-disable-item', function ($event) {
                const data = $event.detail;
                // очистим activeItems от свойств которые нужно удалить
                _this.activeItems = _this.activeItems.filter((e) => !data.items.includes(e));
                _this.clearProp(data.items)
            });
            // событие установки нового значения
            window.addEventListener('filter-change-range', function ($event) {
                _this.addToActiveItems($event.detail.name)
                _this.executeFilter()
            });
        },
        prepareResult(rawData){
            this.arResult = rawData;
            Alpine.store('filter').items = this.arResult['ITEMS'];
            // ищем все активные CONTROL_NAME
            for(let key in this.arResult['ITEMS'])
            {
                const _item = this.arResult['ITEMS'][key];
                if (_item['IS_CHECKED'])
                {
                    _item['VALUES'].forEach(e => {
                        if (e['CHECKED'])
                        {
                            this.addToActiveItems(e['CONTROL_NAME'])
                        }
                    })
                }
            }

            //видимость кнопки очистить фильтр
            this.activeItems.length
                ? this.$refs['clearBtn'].classList.remove('hidden')
                : this.$refs['clearBtn'].classList.add('hidden');
        },
        executeFilter(){
            const _this = this;
            const url = this.arResult['SEF_MODE'] ? this.arResult['JS_FILTER_PARAMS']['SEF_SET_FILTER_URL'] : this.arResult['JS_FILTER_PARAMS']['SEF_DEL_FILTER_URL'];

            fetch(url, {
                method: 'POST',
                body: this.getFilterFormData()
            })
                .then( response => response.json() )
                .then( json => {
                    _this.prepareResult(json)
                    // if (!isMobile()){
                        _this.reloadPage()
                    // }
                } )
                .catch( error => console.error('error:', error))
                .finally(() => {});
        },
        reloadPage(){
            const _this = this;
            this.preloaderContainer.classList.add('loading-popover');
            fetch(this.arResult['JS_FILTER_PARAMS']['SEF_SET_FILTER_URL'], {
                method: 'POST',
                cache: 'no-cache',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({reload_ajax: 'y'})
            })
            .then(response => response.text())
            .then(text => {
                let template = document.createElement('div');
                template.innerHTML = text;
                if (template.querySelector('.ajax_container'))
                {
                    _this.preloaderContainer.innerHTML = template.querySelector('.ajax_container').innerHTML;
                }
                else
                {
                    console.log('product container list not found')
                }
            }).finally(() => {
                if (isMobile()){
                    // _this.openFilter = false
                }
                this.preloaderContainer.classList.remove('loading-popover');
                window.dispatchEvent(new CustomEvent("ajax-finished", {
                    detail: {}
                }));
                history.pushState(null, null, this.arResult['JS_FILTER_PARAMS']['SEF_SET_FILTER_URL']);
            })
        },
        getFilterFormData(){
            const raw = new FormData(this.form);
            const formData =  new FormData();
            for(let pair of raw.entries())
            {
                if ((pair[0].includes('MIN') || pair[0].includes('MAX')) && !this.activeItems.includes(pair[0])) continue;
                if (this.clearItems.includes(pair[0])) continue;
                formData.append(pair[0], pair[1]);
            }

            this.clearItems = [];
            return formData;
        },
        clearProp(disableItems){
            this.clearItems = disableItems;
            this.executeFilter();
        },
        addToActiveItems(controlName){
            if (!this.activeItems.includes(controlName))
            {
                this.activeItems.push(controlName);
            }
        },
        trigger: {
            ['@change']($event)
            {
                this.addToActiveItems($event.target.name)
                this.executeFilter();
            }
        },
        clearButton: {
            ['@click']($event)
            {
                $event.preventDefault();
                window.location.href = this.arResult['JS_FILTER_PARAMS']['SEF_DEL_FILTER_URL'];
            }
        }
    }));

    /**
     * компонент одного элемента в фильтре
     */
    Alpine.data('dropdownItem', ($el, key, isRadio = false, range = false) => ({
        ready: false,
        open: false,
        openPart: false,
        el: '',
        key: '',
        data: {},
        visibleItems: {},
        hiddenItems: {},
        isRadio: false,
        isRange: false,
        stepsSlider: null,
        initStepsSlider: false,
        init(){
            const _this = this;
            this.el = $el;
            this.key = key;
            this.isRadio = isRadio;
            this.isRange = range;

            // следим за изменением данных в хранилище фильтра
            Alpine.effect(() =>
            {
                this.preparedData(Alpine.store('filter').items[this.key])
            });

            // ждем инициализацю фильтра
            window.addEventListener('init-filter', function ($event)
            {
                if (_this.isRange)
                {
                    // инициализация ползунков
                    _this.stepsSlider = rangeSlider(_this.$refs['rangeSlider']);
                    _this.stepsSlider.noUiSlider.on('change.one', function (values, index) {
                        window.dispatchEvent(new CustomEvent("filter-change-range", {
                            detail: {
                                name: _this.data['VALUES'][index]['CONTROL_NAME'],
                                value: _this.data['VALUES'][index]
                            }
                        }));
                    });
                    // отметим что ползунок готов к работе
                    _this.initStepsSlider = true;
                }
                // отметим что компонент полностью готов к работе
                _this.ready = true;
            });
        },
        preparedData(rawData)
        {
            const deepCopy = JSON.parse(JSON.stringify(rawData));
            if (!deepCopy) return;
            this.data = deepCopy;

            if (!this.isRange)
            {
                // разбиваем VALUES на подгруппы
                for (let k in deepCopy['VALUES'])
                {
                    if (k < 3)
                    {
                        // видимые сразу
                        this.visibleItems[k] = deepCopy['VALUES'][k];
                    }
                    else
                    {
                        // видимые по нажатию показать еще
                        this.hiddenItems[k] = deepCopy['VALUES'][k];
                    }
                }
            }
            else
            {
                if (this.initStepsSlider)
                {
                    // обновление ползунков
                    this.range()
                }
            }

            // активность элемента
            deepCopy['IS_CHECKED']
                ? this.el.classList.add('active')
                : this.el.classList.remove('active');
        },
        /**
         * инициализация и работа noUiSlider
         */
        range(){
            const _values = this.data['VALUES'];
            // установим новые значения
            let from = Math.round(_values[0]['VALUE']);
            let to = Math.round(_values[1]['VALUE']);

            if (_values[0]['FILTERED_VALUE'])
            {
                from = Math.round(_values[0]['FILTERED_VALUE']);
            }

            if (_values[1]['FILTERED_VALUE'])
            {
                to = Math.round(_values[1]['FILTERED_VALUE']);
            }

            if (_values[0]['HTML_VALUE'])
            {
                from = Math.round(_values[0]['HTML_VALUE']);
            }

            if (_values[1]['HTML_VALUE'])
            {
                to = Math.round(_values[1]['HTML_VALUE']);
            }

            this.stepsSlider.noUiSlider.updateOptions({
                start: [from, to],
                /*padding: [40, 40],
                range: {
                    'min': [Number(from)],
                    'max': [Number(to)]
                },*/
            }, true);
        },
        reset(){
            if (this.isRadio){
                this.el.querySelector('.emptyValue').click();
                return;
            }

            const find = Alpine.store('filter').items[this.key];
            if (find)
            {
                const disableItems = [];
                find['VALUES'].forEach(e => {
                    disableItems.push(e.CONTROL_NAME);
                });

                if (disableItems.length){
                    window.dispatchEvent(new CustomEvent("filter-disable-item", {
                        detail: {
                            items: disableItems,
                            range: this.isRange
                        }
                    }));
                }
            }
        },
        filterItem: {
            ['@click.away']()
            {
                if (!isMobile()){
                    this.open = false;
                }
            },
            ['@click']($event)
            {
                if ($event.target.classList.contains('filter-item__remove') || $event.target.closest('.filter-item__remove')){
                    this.open = false;
                    this.reset();
                    return;
                }
                if (!$event.target.classList.contains('filter-item__body-list-more')){
                    this.open = !this.open;
                }
            }
        }
    }))
})
