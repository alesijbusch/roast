<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use GoodApp\Google\RecaptchaV3;
use GoodApp\Helpers\Util;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var SlamSaleOrderAjax $component
 * @var string $templateFolder
 */

//CJSCore::Init(array('jquery'));

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();

//RecaptchaV3::getInstance()->init();

$arParams['ALLOW_USER_PROFILES'] = $arParams['ALLOW_USER_PROFILES'] === 'Y' ? 'Y' : 'N';
$arParams['SKIP_USELESS_BLOCK'] = $arParams['SKIP_USELESS_BLOCK'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['SHOW_ORDER_BUTTON'])) {
    $arParams['SHOW_ORDER_BUTTON'] = 'final_step';
}

$arParams['HIDE_ORDER_DESCRIPTION'] = isset($arParams['HIDE_ORDER_DESCRIPTION']) && $arParams['HIDE_ORDER_DESCRIPTION'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_TOTAL_ORDER_BUTTON'] = $arParams['SHOW_TOTAL_ORDER_BUTTON'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] = $arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_PAY_SYSTEM_INFO_NAME'] = $arParams['SHOW_PAY_SYSTEM_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_LIST_NAMES'] = $arParams['SHOW_DELIVERY_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_INFO_NAME'] = $arParams['SHOW_DELIVERY_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_PARENT_NAMES'] = $arParams['SHOW_DELIVERY_PARENT_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_STORES_IMAGES'] = $arParams['SHOW_STORES_IMAGES'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['BASKET_POSITION']) || !in_array($arParams['BASKET_POSITION'], array('before', 'after'))) {
    $arParams['BASKET_POSITION'] = 'after';
}

$arParams['EMPTY_BASKET_HINT_PATH'] = isset($arParams['EMPTY_BASKET_HINT_PATH']) ? (string)$arParams['EMPTY_BASKET_HINT_PATH'] : '/';
$arParams['SHOW_BASKET_HEADERS'] = $arParams['SHOW_BASKET_HEADERS'] === 'Y' ? 'Y' : 'N';
$arParams['HIDE_DETAIL_PAGE_URL'] = isset($arParams['HIDE_DETAIL_PAGE_URL']) && $arParams['HIDE_DETAIL_PAGE_URL'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERY_FADE_EXTRA_SERVICES'] = $arParams['DELIVERY_FADE_EXTRA_SERVICES'] === 'Y' ? 'Y' : 'N';

$arParams['SHOW_COUPONS'] = isset($arParams['SHOW_COUPONS']) && $arParams['SHOW_COUPONS'] === 'N' ? 'N' : 'Y';

if ($arParams['SHOW_COUPONS'] === 'N') {
    $arParams['SHOW_COUPONS_BASKET'] = 'N';
    $arParams['SHOW_COUPONS_DELIVERY'] = 'N';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = 'N';
} else {
    $arParams['SHOW_COUPONS_BASKET'] = isset($arParams['SHOW_COUPONS_BASKET']) && $arParams['SHOW_COUPONS_BASKET'] === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_DELIVERY'] = isset($arParams['SHOW_COUPONS_DELIVERY']) && $arParams['SHOW_COUPONS_DELIVERY'] === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = isset($arParams['SHOW_COUPONS_PAY_SYSTEM']) && $arParams['SHOW_COUPONS_PAY_SYSTEM'] === 'N' ? 'N' : 'Y';
}

$arParams['SHOW_NEAREST_PICKUP'] = $arParams['SHOW_NEAREST_PICKUP'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERIES_PER_PAGE'] = isset($arParams['DELIVERIES_PER_PAGE']) ? intval($arParams['DELIVERIES_PER_PAGE']) : 9;
$arParams['PAY_SYSTEMS_PER_PAGE'] = isset($arParams['PAY_SYSTEMS_PER_PAGE']) ? intval($arParams['PAY_SYSTEMS_PER_PAGE']) : 9;
$arParams['PICKUPS_PER_PAGE'] = isset($arParams['PICKUPS_PER_PAGE']) ? intval($arParams['PICKUPS_PER_PAGE']) : 5;
$arParams['SHOW_PICKUP_MAP'] = $arParams['SHOW_PICKUP_MAP'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_MAP_IN_PROPS'] = $arParams['SHOW_MAP_IN_PROPS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_YM_GOALS'] = $arParams['USE_YM_GOALS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

$useDefaultMessages = !isset($arParams['USE_CUSTOM_MAIN_MESSAGES']) || $arParams['USE_CUSTOM_MAIN_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_BLOCK_NAME'])) {
    $arParams['MESS_AUTH_BLOCK_NAME'] = Loc::getMessage('AUTH_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REG_BLOCK_NAME'])) {
    $arParams['MESS_REG_BLOCK_NAME'] = Loc::getMessage('REG_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BASKET_BLOCK_NAME'])) {
    $arParams['MESS_BASKET_BLOCK_NAME'] = Loc::getMessage('BASKET_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_BLOCK_NAME'])) {
    $arParams['MESS_REGION_BLOCK_NAME'] = Loc::getMessage('REGION_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAYMENT_BLOCK_NAME'])) {
    $arParams['MESS_PAYMENT_BLOCK_NAME'] = Loc::getMessage('PAYMENT_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_BLOCK_NAME'])) {
    $arParams['MESS_DELIVERY_BLOCK_NAME'] = Loc::getMessage('DELIVERY_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BUYER_BLOCK_NAME'])) {
    $arParams['MESS_BUYER_BLOCK_NAME'] = Loc::getMessage('BUYER_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BACK'])) {
    $arParams['MESS_BACK'] = Loc::getMessage('BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FURTHER'])) {
    $arParams['MESS_FURTHER'] = Loc::getMessage('FURTHER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_EDIT'])) {
    $arParams['MESS_EDIT'] = Loc::getMessage('EDIT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER'])) {
    $arParams['MESS_ORDER'] = $arParams['~MESS_ORDER'] = Loc::getMessage('ORDER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PRICE'])) {
    $arParams['MESS_PRICE'] = Loc::getMessage('PRICE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERIOD'])) {
    $arParams['MESS_PERIOD'] = Loc::getMessage('PERIOD_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_BACK'])) {
    $arParams['MESS_NAV_BACK'] = Loc::getMessage('NAV_BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_FORWARD'])) {
    $arParams['MESS_NAV_FORWARD'] = Loc::getMessage('NAV_FORWARD_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ADDITIONAL_MESSAGES']) || $arParams['USE_CUSTOM_ADDITIONAL_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRICE_FREE'])) {
    $arParams['MESS_PRICE_FREE'] = Loc::getMessage('PRICE_FREE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ECONOMY'])) {
    $arParams['MESS_ECONOMY'] = Loc::getMessage('ECONOMY_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGISTRATION_REFERENCE'])) {
    $arParams['MESS_REGISTRATION_REFERENCE'] = Loc::getMessage('REGISTRATION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_1'])) {
    $arParams['MESS_AUTH_REFERENCE_1'] = Loc::getMessage('AUTH_REFERENCE_1_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_2'])) {
    $arParams['MESS_AUTH_REFERENCE_2'] = Loc::getMessage('AUTH_REFERENCE_2_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_3'])) {
    $arParams['MESS_AUTH_REFERENCE_3'] = Loc::getMessage('AUTH_REFERENCE_3_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ADDITIONAL_PROPS'])) {
    $arParams['MESS_ADDITIONAL_PROPS'] = Loc::getMessage('ADDITIONAL_PROPS_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_USE_COUPON'])) {
    $arParams['MESS_USE_COUPON'] = Loc::getMessage('USE_COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_COUPON'])) {
    $arParams['MESS_COUPON'] = Loc::getMessage('COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERSON_TYPE'])) {
    $arParams['MESS_PERSON_TYPE'] = Loc::getMessage('PERSON_TYPE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PROFILE'])) {
    $arParams['MESS_SELECT_PROFILE'] = Loc::getMessage('SELECT_PROFILE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_REFERENCE'])) {
    $arParams['MESS_REGION_REFERENCE'] = Loc::getMessage('REGION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PICKUP_LIST'])) {
    $arParams['MESS_PICKUP_LIST'] = Loc::getMessage('PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NEAREST_PICKUP_LIST'])) {
    $arParams['MESS_NEAREST_PICKUP_LIST'] = Loc::getMessage('NEAREST_PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PICKUP'])) {
    $arParams['MESS_SELECT_PICKUP'] = Loc::getMessage('SELECT_PICKUP_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_INNER_PS_BALANCE'])) {
    $arParams['MESS_INNER_PS_BALANCE'] = Loc::getMessage('INNER_PS_BALANCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER_DESC'])) {
    $arParams['MESS_ORDER_DESC'] = Loc::getMessage('ORDER_DESC_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ERROR_MESSAGES']) || $arParams['USE_CUSTOM_ERROR_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRELOAD_ORDER_TITLE'])) {
    $arParams['MESS_PRELOAD_ORDER_TITLE'] = Loc::getMessage('PRELOAD_ORDER_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SUCCESS_PRELOAD_TEXT'])) {
    $arParams['MESS_SUCCESS_PRELOAD_TEXT'] = Loc::getMessage('SUCCESS_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FAIL_PRELOAD_TEXT'])) {
    $arParams['MESS_FAIL_PRELOAD_TEXT'] = Loc::getMessage('FAIL_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TITLE'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] = Loc::getMessage('DELIVERY_CALC_ERROR_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TEXT'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] = Loc::getMessage('DELIVERY_CALC_ERROR_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'])) {
    $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] = Loc::getMessage('PAY_SYSTEM_PAYABLE_ERROR_DEFAULT');
}

$scheme = $request->isHttps() ? 'https' : 'http';

switch (LANGUAGE_ID) {
    case 'ru':
        $locale = 'ru-RU';
        break;
    case 'ua':
        $locale = 'ru-UA';
        break;
    case 'tk':
        $locale = 'tr-TR';
        break;
    default:
        $locale = 'en-US';
        break;
}

$this->addExternalCss("/html/components-template/form-mixin/style.css");
$this->addExternalCss("/html/components-template/cart/style.css");
if(!$_REQUEST['ORDER_ID']){
    $this->addExternalJs("/html/components-template/cart/script.min.js");
}

function getDivisibleHour(): int
{
    $hour = (int)date('G');

    // Проверяем делимость часа
    if ($hour % 3 === 0) {
        return 2;
    } elseif ($hour % 2 === 0) {
        return 1;
    } else {
        return 0;
    }
}

$keys = (new GoodApp\HighLoad\YandexKey())->getAll();
if ($key = $keys[getDivisibleHour()]) { ?>
    <script id="customMap"
            src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=<?= $key['UF_KEY'] ?>&suggest_apikey=<?= $key['UF_SUGGEST_KEY'] ?>"></script>
<?php } ?>
    <NOSCRIPT>
        <div style="color:red"><?= Loc::getMessage('SOA_NO_JS') ?></div>
    </NOSCRIPT>


<?
if ($request->get('ORDER_ID') <> '') {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/confirm.php');
} elseif ($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/empty.php');
} else {
    $hideDelivery = empty($arResult['DELIVERY']);

    $signer = new Main\Security\Sign\Signer;
    $signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.order.ajax.custom');
    $messages = Loc::loadLanguageFile(__FILE__);

    $jsParams = [
        'result' => $arResult['JS_DATA'],
        'locations' => $arResult['LOCATIONS'],
        'params' => $arParams,
        'signedParamsString' => $signedParams,
        'siteID' => $component->getSiteId(),
        'ajaxUrl' => $component->getPath() . '/ajax.php',
        'templateFolder' => $templateFolder,
        'action' => 'saveOrderAjax',
        'formSelector' => '#bx-soa-order-form',
        'noPhoto' => '/upload/default.png',
    ];
    ?>
    <div class="cart"  x-data='saleOrderAjax(<?= \Bitrix\Main\Web\Json::encode($jsParams) ?>)'>
        <div class="rowblock">
            <div class="rowblock__left">
                <div class="cart-form__block" :class="{'collapse': !expand.products}">
                    <div class="cart-form__header">
                        <div class="cart-form__title">Товары в корзине</div>
                        <div class="cart-form__toggler" @click="expand.products=!expand.products"><span></span></div>
                    </div>
                    <template x-if="productList.length">
                        <div class="cart-products cart-form__section" x-transition>
                            <template x-for="product in productList" :key="product.ID">
                                <div class="cart-product" :class="{'loading-popover': product.PRELOADER}">
                                    <div class="cart-product__image">
                                        <a class="lazy-img-wrap" :href="product.DETAIL_PAGE_URL">
                                            <img :src="product.DISPLAY_PICTURE" :alt="product.NAME"/>
                                        </a>
                                    </div>
                                    <div class="cart-product__content">
                                        <a class="cart-product__title" :href="product.DETAIL_PAGE_URL"
                                           x-html="product.NAME"></a>
                                    </div>
                                    <div class="cart-product__prop">
                                        <template
                                                x-if="product.PROPS.filter(item => item.CODE == 'IS_GIFT' && item.VALUE == 'Y').length == 0">
                                            <template x-for="prop in product.PROPS" :key="prop.ID">
                                                <div class="cart-product__type"
                                                     :class="{'hidden': prop.NAME == 'PARENT_ID'}">
                                                    <div class="cart-product__type-prop"
                                                         x-html="prop.NAME + ': '"></div>
                                                    <div class="cart-product__type-val active"
                                                         x-html="prop.VALUE"></div>
                                                </div>
                                            </template>
                                        </template>
                                        <template
                                                x-if="product.PROPS.filter(item => item.CODE == 'IS_GIFT' && item.VALUE == 'Y').length > 0">
                                            <div class="grid-list__item-select">
                                                <div class="grid-list__item-title offer-item-line-js"
                                                     :data-prop-code="'STEPEN_POMOLA'" x-ref="propLvl1"
                                                     x-text="'Степень помола'"></div>

                                                <div class="select" x-data="{isOpenedSelect: false}"
                                                     :class="{opened: isOpenedSelect}"
                                                     @click.away="isOpenedSelect = false">
                                                    <div class="select__header"
                                                         @click="isOpenedSelect = ! isOpenedSelect">
                                                        <div class="select__title offer-item-title-js"><span
                                                                    x-text="product.PROPS.find(item => item.CODE == 'STEPEN_POMOLA')?.VALUE"></span>
                                                            <svg class="icon" style="height:5px;width:7px;">
                                                                <use xlink:href="/html/images/sprite.svg#i-arrow-down"></use>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <div class="select__body">
                                                        <ul class="select__body-list">
                                                            <template
                                                                    x-for="value in ['Не молоть', 'Аэропресс', 'Мока', 'Под чашку', 'Пуровер', 'Турка', 'Френч-пресс', 'Эспрессо крупно', 'Эспрессо тонко']">
                                                                <li class="offer-value-js"
                                                                    @click="changeBasketProperty(product.ID, value, 'STEPEN_POMOLA');isOpenedSelect=false"
                                                                >
                                                                    <span class="text" :title="value"
                                                                          x-text="value"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="cart-product__price">
                                        <div class="price"
                                             x-html="product.PRICE_FORMATED + '/' + product.MEASURE_TEXT"></div>
                                        <template x-if="product.DISCOUNT_PRICE > 0">
                                            <div class="price--old"
                                                 x-html="product.BASE_PRICE_FORMATED + '/' + product.MEASURE_TEXT"></div>
                                        </template>
                                    </div>
                                    <div class="cart-product__counter">
                                        <div class="counter btn-wave"
                                             :class="{'hidden': product.IS_POMOL}"
                                             x-data="counter(product.MEASURE_RATIO, product.AVAILABLE_QUANTITY, product.QUANTITY, product.MEASURE_RATIO_VALUE, product.ID)"
                                             x-effect="count = product.QUANTITY"
                                        >
                                            <span class="counter__action counter__action--minus" @click="decrement"
                                                  :disabled="disableMin || product.PRELOADER"></span>
                                            <input class="counter__count" type="text" x-bind="watchCounter"
                                                   :value="count" maxlength="6"/>
                                            <span class="counter__action counter__action--plus" @click="increment"
                                                  :disabled="disableMax || product.PRELOADER"></span>
                                        </div>
                                    </div>
                                    <div class="cart-product__totalprice">
                                        <div class="price" x-html="product.SUM"></div>
                                        <template x-if="product.SUM_DISCOUNT_DIFF > 0">
                                            <div class="price--old" x-html="product.SUM_BASE_FORMATED"></div>
                                        </template>
                                    </div>
                                    <div class="cart-product__control">
                                        <? /*
				<div class="cart-product__control-item" :class="{'active': product['FAVORITE']}" @click="editFavorite($el)">
					<svg class="icon" style="width: 18px; height: 18px;">
						<use xlink:href="/html/images/sprite.svg#i-favorite"></use>
					</svg>
				</div>*/
                                        ?>
                                        <div class="cart-product__control-item" :class="{'hidden': product.IS_POMOL}"
                                             @click="removeItem(product.ID)">
                                            <svg class="icon" style="width: 18px; height: 18px;">
                                                <use xlink:href="/html/images/sprite.svg#i-close"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" name="ORDER_FORM" :class="mk && 'mk'" class="cart-form"
                      id="bx-soa-order-form" enctype="multipart/form-data" <? /* @submit="completeOrder($event)" */ ?>>
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="utm_source" value="">
                    <input type="hidden" name="utm_medium" value="">
                    <input type="hidden" name="utm_campaign" value="">
                    <input type="hidden" name="utm_content" value="">
                    <input type="hidden" name="utm_term" value="">
                    <input type="hidden" :name="params.ACTION_VARIABLE" :value="action">
                    <input type="hidden" name="BUYER_STORE" value="0">
                    <input type="hidden" name="PROFILE_ID" x-model="activeProfile.ID">
                    <input type="hidden" name="location_type" value="code">
                    <input type="hidden" id="STEPEN_POMOLA_GIFT" :name="'ORDER_PROP_' + stepenPomolaGift.ID"
                           :value="stepenPomolaGift['VALUE'][0]"/>

                    <div class="cart-form__block cart-form__block--person" :class="{'collapse': !expand.user}">
                        <div class="cart-form__header">
                            <div class="cart-form__title">Покупатель</div>

                            <div class="person-type">
                                <template x-for="person in result.PERSON_TYPE">
                                    <div class="form-group form-group--person-type">
                                        <div class="radio">
                                            <input type="radio" :id="'PERSON_TYPE_' + person.ID" x-model="PERSON_TYPE"
                                                   :value="person.ID"/>
                                            <label :for="'PERSON_TYPE_' + person.ID">
                                                <div class="person-type__label" x-html="person.NAME"></div>
                                            </label>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="cart-form__section">
                            <div class="cart-form__form">
                                <template x-for="prop in orderPropsList" :key="prop.ID">
                                    <div class="cart-form__field cart-form__field--2"
                                         :class="{'cart-form__field-location': (prop.CODE == 'PD_LOCATION' || prop.CODE == 'LOCATION')}"
                                         x-data="{search: false}" @click.outside="search=false">
                                        <template
                                                x-if="prop.CODE != 'PD_LOCATION' && prop.CODE != 'LOCATION' && prop.CODE!='PICKUP_POINT'">
                                            <div class="form-group">
                                                <label class="form-control-label" :for="prop.CODE"
                                                       x-html="prop.REQUIRED === 'Y' ? prop.NAME + `<span class='label-required'>*</span>` : prop.NAME"></label>
                                                <input class="form-control" :type="prop.INPUT_TYPE" :id="prop.CODE"
                                                       :name="'ORDER_PROP_' + prop.ID" :value="prop['VALUE'][0]"
                                                       :required="prop.REQUIRED === 'Y'" placeholder=""/>
                                            </div>
                                        </template>
                                        <template x-if="prop.CODE == 'PICKUP_POINT'">
                                            <div class="form-group">
                                                <input class="form-control" type="hidden" :id="prop.CODE"
                                                       :name="'ORDER_PROP_' + prop.ID" :value="prop['VALUE'][0]"
                                                       :required="prop.REQUIRED === 'Y'" placeholder=""/>
                                            </div>
                                        </template>
                                        <template x-if="prop.CODE == 'PD_LOCATION' || prop.CODE == 'LOCATION'">
                                            <div class="form-group"
                                                 :class="{'is-invalid' : !location, 'is-success': location }">
                                                <label
                                                        class="form-control-label"
                                                        :for="prop.CODE + '_selector'"
                                                        x-html="prop.REQUIRED === 'Y' ? prop.NAME + `<span class='label-required'>*</span>` : prop.NAME"
                                                ></label>
                                                <input
                                                        x-on:focus="search=true"
                                                        @input.debounce.400="guessCity($event)"
                                                        class="form-control location-selector"
                                                        :type="prop.INPUT_TYPE"
                                                        :id="prop.CODE + '_selector'"
                                                        :required="prop.REQUIRED === 'Y'"
                                                        placeholder=""
                                                        x-ref="locationFake"
                                                        autocomplete="nope"
                                                />

                                                <input
                                                        type="hidden"
                                                        class="location-hidden"
                                                        :id="prop.CODE"
                                                        :name="'ORDER_PROP_' + prop.ID"
                                                        :required="prop.REQUIRED === 'Y'"
                                                        x-ref="locationReal"
                                                />
                                                <template x-if="citiesList">
                                                    <div class="cart-form__field-dropdown" x-show="search"
                                                         x-transition="">
                                                        <template x-for="city in citiesList">
                                                            <div class="cart-form__field-item"
                                                                 @click="changeLocation(city); search=false;"
                                                                 x-text="city.title"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="Object.values(userConsent).length">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input class="form-control" type="checkbox" :id="userConsent.CODE"
                                                   :name="'ORDER_PROP_' + userConsent.ID" value="Y"
                                                   :required="userConsent.REQUIRED === 'Y'"/>
                                            <label class="form-control-label" :for="userConsent.CODE"
                                                   x-html="'Обработка персональных <a href=\'/info/public/\' target=\'_blank\'>данных</a>' + (userConsent.REQUIRED === 'Y' ? `<span class='label-required'>*</span>` : '')"></label>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="cart-form__block-grid" :class="location && 'enabled'">
                        <div id="DELIVERY_ID" class="cart-form__block"
                             :class="{'collapse': !expand.delivery, 'error-msg': !DELIVERY_ID}">
                            <div class="cart-form__header">
                                <div class="warning-img">
                                    <img src="/html/images/warning.png" alt="" width="32">
                                </div>
                                <div class="cart-form__title">Способ доставки</div>
                                <div class="cart-form__toggler" @click="expand.delivery=!expand.delivery"><span></span>
                                </div>
                            </div>
                            <div class="alert alert-danger">
                                <div>Выберите способ доставки</div>
                            </div>
                            <div class="cart-form__delivery">
                                <template x-for="group in deliveryGroupsList">
                                    <template x-if="group.current">
                                        <div class="cart-form-radio">
                                            <div class="form-group cart-form-radio__header">
                                                <div class="radio">
                                                    <input type="radio" :id="'DELIVERY_ID_' + group.ID"
                                                           x-model="DELIVERY_ID" :value="group.current.ID"/>
                                                    <label :for="'DELIVERY_ID_' + group.ID">
                                                        <div class="cart-form-radio__label" x-html="group.NAME"></div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="cart-form-radio__body">
                                                <div class="cart-form__delivery-descr"
                                                     x-html="group.current.DESCRIPTION"></div>

                                                <div class="cart-city-picker">
                                                    <div class="select" x-data="{isOpenedSelect: false}"
                                                         :class="{'opened': isOpenedSelect}"
                                                         @click.away="isOpenedSelect = false">
                                                        <div class="select__header"
                                                             @click="isOpenedSelect = !isOpenedSelect">
                                                            <div class="select__title">
                                                                <span x-html="group.current.NAME"></span>
                                                                <svg class="icon" style="height:5px;width:7px;">
                                                                    <use xlink:href="/html/images/sprite.svg#i-arrow-down"></use>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div class="select__body">
                                                            <ul class="select__body-list">
                                                                <template x-for="val in group.values" :key="val.id">
                                                                    <li @click="isOpenedSelect = !isOpenedSelect; changeDeliveryGroupValue(group, val)">
                                                                        <span x-html="val.name"></span>
                                                                    </li>
                                                                </template>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </template>

                                <template x-for="delivery in deliveryList" :key="delivery.ID">
                                    <template x-if="!deliveryGroupsIds.includes(delivery.PARENT_ID)">
                                        <div class="cart-form-radio">
                                            <div class="form-group cart-form-radio__header">
                                                <div class="radio">
                                                    <input type="radio" :id="'DELIVERY_ID_' + delivery.ID"
                                                           x-model="DELIVERY_ID" :value="delivery.ID"/>
                                                    <label :for="'DELIVERY_ID_' + delivery.ID">
                                                        <div class="cart-form-radio__label"
                                                             x-html="delivery.NAME"></div>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="cart-form-radio__body">
                                                <template x-if="delivery.PRICE_FORMATED">
                                                    <div class="cart-form__delivery-cost"
                                                         x-html="delivery.PRICE_FORMATED"></div>
                                                </template>
                                                <template x-if="delivery.PERIOD_TEXT">
                                                    <div class="cart-form__delivery-period"
                                                         x-html="delivery.PERIOD_TEXT"></div>
                                                </template>
                                                <div class="cart-form__delivery-descr"
                                                     x-html="delivery.DESCRIPTION"></div>
                                                <template x-if="delivery.CALCULATE_ERRORS">
                                                    <div class="cart-form__delivery-descr"
                                                         x-html="delivery.CALCULATE_ERRORS"></div>
                                                </template>
                                                <template
                                                        x-if="delivery.TO_TERMINAL_EUROPOST =='Y' && pickup?.items?.length > 0">
                                                    <div class="pickup-point">
                                                        <div class="pickup-point__address" x-text="pickup.current?.PRM?.WarehouseName"></div>
                                                        <div class="btn btn--primary" @click="openModalPickup">Выбрать другой</div>


                                                    </div>


                                                </template>
                                            </div>

                                            <template x-if="DELIVERY_ID == delivery.ID && isTimeForOnlinePay">
                                                <div class="form-group cart-time-picker">
                                                    <label class="form-control-label" :for="timeForOnlinePay.CODE"
                                                           x-html="timeForOnlinePay.REQUIRED === 'Y' ? timeForOnlinePay.NAME + `<span class='label-required'>*</span>` : timeForOnlinePay.NAME"></label>
                                                    <input type="hidden" :id="timeForOnlinePay.CODE"
                                                           :name="'ORDER_PROP_' + timeForOnlinePay.ID"
                                                           :value="timeForOnlinePay['VALUE'][0]"
                                                           :required="timeForOnlinePay.REQUIRED === 'Y'"
                                                           placeholder=""/>
                                                    <div class="select" x-data="{isOpenedSelect: false}"
                                                         :class="{'opened': isOpenedSelect}"
                                                         @click.away="isOpenedSelect = false">
                                                        <div class="select__header"
                                                             @click="isOpenedSelect = !isOpenedSelect">
                                                            <div class="select__title">
                                                                <span x-html="timeForOnlinePay.OPTIONS[timeForOnlinePay.VALUE[0]]"></span>
                                                                <svg class="icon" style="height:5px;width:7px;">
                                                                    <use xlink:href="/html/images/sprite.svg#i-arrow-down"></use>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div class="select__body">
                                                            <ul class="select__body-list">
                                                                <template x-for="val in timeForOnlinePay.OPTIONS_SORT">
                                                                    <li @click="isOpenedSelect = !isOpenedSelect; timeForOnlinePay.VALUE[0] = val;">
                                                                        <span x-html="timeForOnlinePay.OPTIONS[val]"></span>
                                                                    </li>
                                                                </template>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                        <div id="PAY_SYSTEM_ID" class="cart-form__block" :class="{'error-msg': !PAY_SYSTEM_ID}">
                            <div class="cart-form__header">
                                <div class="warning-img">
                                    <img src="/html/images/warning.png" alt="" width="32">
                                </div>
                                <div class="cart-form__title">Способ оплаты
                                    <div class="cart-form__toggler" @click="expand.pay=!expand.pay"><span></span></div>
                                </div>
                            </div>

                            <div class="alert alert-danger">
                                <div>Выберите способ оплаты</div>
                            </div>

                            <div class="cart-form__pay">
                                <template x-for="paySystem in paySystemList" :key="paySystem.PAY_SYSTEM_ID">
                                    <div class="cart-form-radio">
                                        <div class="form-group cart-form-radio__header">

                                            <div class="radio">
                                                <input type="radio" :id="'PAY_SYSTEM_' + paySystem.PAY_SYSTEM_ID"
                                                       x-model="PAY_SYSTEM_ID" :value="paySystem.PAY_SYSTEM_ID"/>
                                                <label :for="'PAY_SYSTEM_' + paySystem.PAY_SYSTEM_ID">
                                                    <div class="cart-form-radio__label" x-html="paySystem.NAME"></div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="cart-form-radio__body">
                                            <div class="cart-form__delivery-descr" x-html="paySystem.DESCRIPTION"></div>

                                            <template
                                                    x-if="result.CURRENT_BUDGET_FORMATED&&paySystem.PAY_SYSTEM_ID==PAY_SYSTEM_ID">
                                                <div class="policy-block">
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
                                                            <input type="checkbox" id="PAY_CURRENT_ACCOUNT" value="Y"
                                                                   name="PAY_CURRENT_ACCOUNT"
                                                                   x-model="PAY_CURRENT_ACCOUNT">
                                                            <label for="PAY_CURRENT_ACCOUNT"
                                                                   x-html="`Внутренний счет - <b>${result.CURRENT_BUDGET_FORMATED}</b>`"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <template x-if="address">
                        <div class="cart-form__block" :class="{'collapse': !expand.address}">
                            <div class="cart-form__header">
                                <div class="cart-form__title"
                                     x-html="address.REQUIRED === 'Y' ? address.NAME + `<span class='label-required'>*</span>` : address.NAME"></div>
                                <div class="cart-form__toggler" @click="expand.address=!expand.address"><span></span>
                                </div>
                            </div>

                            <div class="form-group" @click.outside="showYaSuggestList=false">
                                <label
                                        class="form-control-label"
                                        :for="address.CODE"
                                        x-html="address.REQUIRED === 'Y' ? address.NAME + `<span class='label-required'>*</span> (` + cityData.name + `)` : address.NAME">
                                </label>
                                <input
                                        class="form-control"
                                        :type="address.INPUT_TYPE"
                                        :id="address.CODE"
                                        :name="'ORDER_PROP_' + address.ID"
                                        :value="address['VALUE'][0]"
                                        :required="address.REQUIRED === 'Y'"
                                        @input.debounce.400="guessAddress($event)"
                                        @click="guessAddress($event)"
                                        placeholder=""
                                />

                                <template x-if="yaSuggestList">
                                    <div class="cart-form__field-dropdown" x-show="showYaSuggestList" x-transition="">
                                        <template x-for="suggest in yaSuggestList">
                                            <div class="cart-form__field-item" @click="changeAddress($event, suggest);"
                                                 x-text="suggest.displayName"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <template x-for="prop in addressDataHidden">
                                <div class="form-group">
                                    <input class="form-control" type="hidden" :id="prop.CODE"
                                           :name="'ORDER_PROP_' + prop.ID" :value="prop['VALUE'][0]"
                                           :required="prop.REQUIRED === 'Y'" placeholder=""/>
                                </div>
                            </template>

                            <template x-for="prop in addressData">
                                <div class="form-group">
                                    <label class="form-control-label" :for="prop.CODE"
                                           x-html="prop.REQUIRED === 'Y' ? prop.NAME + `<span class='label-required'>*</span>` : prop.NAME"></label>
                                    <input
                                            class="form-control"
                                            :type="prop.INPUT_TYPE"
                                            :id="prop.CODE"
                                            :name="'ORDER_PROP_' + prop.ID"
                                            :value="prop['VALUE'][0]"
                                            :required="prop.REQUIRED === 'Y'" placeholder=""
                                    />
                                </div>
                            </template>

                            <div id="delivery_map"></div>
                        </div>
                    </template>

                    <template x-if="propBes||propNeperez||commentary">
                        <div class="cart-form__block cart-form__block--comment"
                             :class="{'collapse': !expand.props, 'enabled': location}">
                            <template x-if="commentary">
                                <div class="cart-form__header">
                                    <div class="cart-form__title"
                                         x-html="commentary.REQUIRED === 'Y' ? commentary.NAME + `<span class='label-required'>*</span>` : commentary.NAME"></div>
                                    <div class="cart-form__toggler" @click="expand.props=!expand.props"><span></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="propBes">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="hidden" :name="'ORDER_PROP_' + propBes.ID" value="N">
                                        <input class="form-control" type="checkbox" :id="propBes.CODE"
                                               :name="'ORDER_PROP_' + propBes.ID" value="Y"
                                               :required="propBes.REQUIRED === 'Y'"
                                               :checked="propBes.VALUE[0] === 'Y'"/>
                                        <label class="form-control-label" :for="propBes.CODE"
                                               x-html="propBes.REQUIRED === 'Y' ? propBes.NAME + `<span class='label-required'>*</span>` : propBes.NAME"></label>
                                    </div>
                                </div>
                            </template>

                            <template x-if="propNeperez">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="hidden" :name="'ORDER_PROP_' + propNeperez.ID" value="N">
                                        <input class="form-control" type="checkbox" :id="propNeperez.CODE"
                                               :name="'ORDER_PROP_' + propNeperez.ID" value="Y"
                                               :required="propNeperez.REQUIRED === 'Y'"
                                               :checked="propNeperez.VALUE[0] === 'Y'"/>
                                        <label class="form-control-label" :for="propNeperez.CODE"
                                               x-html="propNeperez.REQUIRED === 'Y' ? propNeperez.NAME + `<span class='label-required'>*</span>` : propNeperez.NAME"></label>
                                    </div>
                                </div>
                            </template>

                            <template x-if="commentary">
                                <div class="cart-form__section">
                                    <div class="cart-form__form-desc" x-html="commentary.DESCRIPTION"></div>

                                    <div class="cart-form__form">
                                        <div class="cart-form__field">
                                            <div class="form-group">
                                                <textarea class="form-control" :type="commentary.INPUT_TYPE"
                                                          :id="commentary.CODE" :name="'ORDER_PROP_' + commentary.ID"
                                                          :value="commentary['VALUE'][0]"
                                                          :required="commentary.REQUIRED === 'Y'"
                                                          placeholder=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-teleport=".js-modals-list">
                        <div class="modal fade modal-pickup modal-wide " x-ref="modalPickup" id="modal-pickup"
                             tabindex="-1" aria-modal="true" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <button class="modal-close" type="button" data-dismiss="modal" aria-label="Close">
                                        <svg class="icon" style="width: 14px; height: 14px;">
                                            <use xlink:href="/html/images/sprite.svg#i-close"></use>
                                        </svg>
                                    </button>

                                    <div class="modal-body">
                                        <div class="modal-pickup__grid">
                                            <div class="modal-pickup__list">
                                                <li class="modal-pickup__title">Выберите пункт выдачи</li>
                                                <div class="modal-pickup__ovh">
                                                <ul class="modal-pickup__points">
                                                    <template x-for="val, index in pickup.items">
                                                        <li class="modal-pickup__item" @click="clickPoint(val, index)" :class="tempPoint === val && 'active'">
                                                            <span x-html="val.PRM.WarehouseName"></span>
                                                            <span class="round"></span>
                                                        </li>
                                                    </template>
                                                    <div class="modal-pickup__footer">
                                                        <div class="btn btn--primary" :disabled="tempPoint === pickup.current" @click="submitPoint">Привезти сюда</div>
                                                    </div>
                                                </ul>
                                                </div>
                                            </div>

                                            <div id="pickup-map" class="modal-pickup__map" style="width: 100%; height: 600px"></div>

                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>
            <div class="rowblock__right">

                <div class="cart-bill">
                    <div class="cart-bill__price cart-checkout__total-line cart-checkout__total-line--totals">
                        <div class="cart-checkout__d">Итого:</div>
                        <div class="cart-checkout__t" x-html="result.TOTAL.ORDER_TOTAL_PRICE_FORMATED"></div>
                    </div>
                    <div class="cart-bill__btn">
                        <div class="btn" @click="dispatchCompleteOrder()">Оформить</div>
                    </div>
                </div>

                <div class="cart-checkout-sticky">
                    <div class="cart-checkout-wrap" :class="{'loading-popover': preloaderTotal}">
                        <div class="cart-checkout">
                            <div class="cart-checkout__title">Ваш заказ</div>
                            <template x-if="DELIVERY_ID">
                                <div class="cart-checkout__total-line">
                                    <div class="cart-checkout__d">Доставка:</div>
                                    <div class="cart-checkout__t"
                                         x-html="result.TOTAL.DELIVERY_PRICE > 0 ? result.TOTAL.DELIVERY_PRICE_FORMATED : 'Бесплатно'"></div>
                                </div>
                            </template>
                            <? /* <div class="cart-checkout__total-line">
					<div class="cart-checkout__d">Доставка:</div>
					<div class="cart-checkout__t"><span x-html="result['DELIVERY'][DELIVERY_ID]['NAME']"></span></div>
				</div> */ ?>
                            <template
                                    x-if="DELIVERY_ID && result['DELIVERY'][DELIVERY_ID] && result['DELIVERY'][DELIVERY_ID]['PERIOD_TEXT']">
                                <div class="cart-checkout__delivery-info"
                                     x-html="result['DELIVERY'][DELIVERY_ID]['PERIOD_TEXT']"></div>
                            </template>

                            <template x-if="result['TOTAL']['PAY_SYSTEM']">
                                <div class="cart-checkout__total-line">
                                    <div class="cart-checkout__d">Оплата:</div>
                                    <div class="cart-checkout__t"><span x-html="result['TOTAL']['PAY_SYSTEM']"></span>
                                    </div>
                                </div>
                            </template>
                            <div class="cart-checkout__promo">
                                <div class="form-group">
                                    <input class="form-control" type="text" x-model="promoCode" x-ref="coupon"
                                           placeholder="Промокод"/>
                                </div>
                                <button class="cart-checkout__promo-submit" x-cloak x-show="promoCode.length"
                                        @click="enterCoupon()"></button>
                            </div>

                            <template x-if="couponList.length">
                                <div class="cart-checkout__promo-list">
                                    <template x-for="coupon in couponList">
                                        <div class="cart-checkout__promo-item">
                                            <div class="cart-checkout__promo-item-name" x-html="coupon.COUPON"></div>
                                            <div class="cart-checkout__promo-item-status">
                                                <span :class="coupon.JS_STATUS === 'APPLIED' ? 'in-stock' : 'outin-stock'"
                                                      x-html="coupon.JS_CHECK_CODE"></span>

                                                <div class="cart-checkout__promo-item-remove"
                                                     @click="editCoupon(coupon.COUPON)">
                                                    <svg class="icon" style="width: 10px; height: 10px;">
                                                        <use xlink:href="/html/images/sprite.svg#i-close"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="result.TOTAL.PRICE_WITHOUT_DISCOUNT">
                                <div class="cart-checkout__total-line">
                                    <div class="cart-checkout__d">Стоимость:</div>
                                    <div class="cart-checkout__t" x-html="result.TOTAL.PRICE_WITHOUT_DISCOUNT"></div>
                                </div>
                            </template>

                            <template x-if="result.TOTAL.PAYED_FROM_ACCOUNT_FORMATED">
                                <div class="cart-checkout__total-line">
                                    <div class="cart-checkout__d">Внутренний счет:</div>
                                    <div class="cart-checkout__t"
                                         x-html="result.TOTAL.PAYED_FROM_ACCOUNT_FORMATED"></div>
                                </div>
                            </template>

                            <template x-if="result.TOTAL.PRICE_WITHOUT_DISCOUNT_VALUE && result.TOTAL.DISCOUNT_PRICE">
                                <div class="cart-checkout__total-line">
                                    <div class="cart-checkout__d"
                                         x-html="`Ваша скидка ${result.TOTAL.print.discountRate}:`"></div>
                                    <div class="cart-checkout__t" x-html="result.TOTAL.DISCOUNT_PRICE_FORMATED"></div>
                                    <div class="tip">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                             viewBox="0 0 16 15" fill="none">
                                            <circle cx="7.61523" cy="7.5" r="6.75" stroke="#D9D9D9"
                                                    stroke-width="1.5"></circle>
                                            <line x1="7.5957" y1="5.96594" x2="7.5957" y2="11.3353" stroke="#ACACAC"
                                                  stroke-width="1.5"></line>
                                            <line x1="7.5957" y1="3.6648" x2="7.5957" y2="5.19889" stroke="#ACACAC"
                                                  stroke-width="1.5"></line>
                                        </svg>
                                        <div class="tip__text">Здесь учтены все скидки для вашего заказа: <a
                                                    href="/info/sales" style="color: #8a6048">это могут быть</a> бонусы
                                            от программы лояльности, скидка за выбранный способ оплаты, промокоды,
                                            акционные товары.
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <? /*<template x-if="result.TOTAL.DISCOUNT_PRICE">
				<div class="cart-checkout__total-line">
					<div class="cart-checkout__d">Промокод:</div>
					<div class="cart-checkout__t" x-html="result.TOTAL.DISCOUNT_PRICE_FORMATED"></div>
				</div>
				</template>
				*/ ?>

                            <div class="cart-checkout__total-line cart-checkout__total-line--totals">
                                <div class="cart-checkout__d">Итого:</div>
                                <div class="cart-checkout__t"
                                     x-html="result.TOTAL.ORDER_TOTAL_LEFT_TO_PAY_FORMATED||result.TOTAL.ORDER_TOTAL_PRICE_FORMATED"></div>
                                <template x-if="result.TOTAL.BASKET_PRICE_DISCOUNT_DIFF_VALUE">
                                    <span class="sale" x-html="result.TOTAL.print.all"></span>
                                </template>
                                <template x-if="result.TOTAL.BASKET_PRICE_DISCOUNT_DIFF_VALUE">
                                    <div class="cart-checkout__sale"
                                         x-html="`<span>Общая экономия ${result.TOTAL.print.saving}</span>`"></div>
                                </template>
                            </div>

                            <div class="cart-checkout__submit">
                                <div class="btn btn--primary" @click="dispatchCompleteOrder()">Оформить заказ</div>
                            </div>
                        </div>

                        <div class="cart-delivery">
                            <? [$roastDay, $neededDay] = Util::getDateRoastNeeded(); ?>
                            <template x-if="isCoffeExist">
                                <div class="cart-delivery__item">
                                    <div class="cart-delivery__icon">
                                        <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.85 2.5L1 5.33333M20 5.33333L17.15 2.5M4.8 17.6111L2.9 19.5M16.2 17.6111L18.1 19.5M7.65 12.4167L9.55 14.3056L13.825 10.0556M10.5 19.5C12.5156 19.5 14.4487 18.704 15.874 17.287C17.2993 15.8701 18.1 13.9483 18.1 11.9444C18.1 9.94059 17.2993 8.0188 15.874 6.60186C14.4487 5.18492 12.5156 4.38889 10.5 4.38889C8.48435 4.38889 6.55126 5.18492 5.12599 6.60186C3.70071 8.0188 2.9 9.94059 2.9 11.9444C2.9 13.9483 3.70071 15.8701 5.12599 17.287C6.55126 18.704 8.48435 19.5 10.5 19.5Z"
                                                  stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="cart-delivery__content">
                                        <div class="prop">Ближайшая обжарка</div>
                                        <div class="val"><?= $roastDay ?></div>
                                    </div>
                                </div>
                            </template>
                            <div class="cart-delivery__item">
                                <div class="cart-delivery__icon">
                                    <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.4 6.3125H14.6204C14.8528 6.3125 14.9689 6.3125 15.0783 6.3384C15.1752 6.36137 15.2679 6.39925 15.3529 6.45065C15.4487 6.50863 15.5309 6.5897 15.6952 6.75184L19.5548 10.5607C19.7191 10.7228 19.8013 10.8039 19.86 10.8985C19.9121 10.9824 19.9505 11.0738 19.9738 11.1695C20 11.2774 20 11.392 20 11.6213V14.2812C20 14.7181 20 14.9365 19.9277 15.1088C19.8313 15.3385 19.6463 15.521 19.4135 15.6161C19.239 15.6875 19.0176 15.6875 18.575 15.6875M13.825 15.6875H12.4M12.4 15.6875V6.5C12.4 5.4499 12.4 4.92485 12.1929 4.52377C12.0108 4.17096 11.7201 3.88413 11.3626 3.70436C10.9561 3.5 10.4241 3.5 9.36 3.5H4.04C2.9759 3.5 2.44385 3.5 2.03742 3.70436C1.67991 3.88413 1.38925 4.17096 1.20709 4.52377C1 4.92485 1 5.4499 1 6.5V13.8125C1 14.848 1.85066 15.6875 2.9 15.6875M12.4 15.6875H8.6M8.6 15.6875C8.6 17.2408 7.32401 18.5 5.75 18.5C4.17599 18.5 2.9 17.2408 2.9 15.6875M8.6 15.6875C8.6 14.1342 7.32401 12.875 5.75 12.875C4.17599 12.875 2.9 14.1342 2.9 15.6875M18.575 16.1562C18.575 17.4507 17.5117 18.5 16.2 18.5C14.8883 18.5 13.825 17.4507 13.825 16.1562C13.825 14.8618 14.8883 13.8125 16.2 13.8125C17.5117 13.8125 18.575 14.8618 18.575 16.1562Z"
                                              stroke="black" stroke-width="1.5" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="cart-delivery__content">
                                    <div class="prop">Ближайшая доставка</div>
                                    <div class="val"><?= $neededDay ?></div>
                                </div>
                            </div>
                        </div>
                        <template x-if="result.CURRENT_BUDGET_FORMATED">
                            <div class="cart-checkout__info" x-data="{show:true}" x-show="show" x-transition="">
                                <div class="cart-checkout__info-close" @click="show=false">
                                    <svg class="icon" style="width:10px; height:10px;">
                                        <use xlink:href="/html/images/sprite.svg#i-close"></use>
                                    </svg>
                                </div>
                                <div class="cart-checkout__info-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="25" viewBox="0 0 17 25"
                                         fill="none">
                                        <path d="M9.30273 24.0698H7.94291V20.8175H9.30273V24.0698Z"
                                              fill="#BB2749"></path>
                                        <path d="M14.2461 22.9749L13.0522 23.6836L11.5631 20.7091L12.757 20.0004L14.2461 22.9749Z"
                                              fill="#BB2749"></path>
                                        <path d="M5.68359 20.7086L4.19451 23.6831L3.0006 22.9744L4.48967 19.9999L5.68359 20.7086Z"
                                              fill="#BB2749"></path>
                                        <path d="M17 17.1064L15.2739 12.6684V6.77378C15.2739 3.0387 12.2352 0 8.5001 0C4.76482 0 1.72612 3.0387 1.72612 6.77378V12.6684L0 17.1064H5.6756V17.2435C5.6756 18.8007 6.94267 20.0678 8.5001 20.0678C10.0573 20.0678 11.3244 18.8007 11.3244 17.2435V17.1064H17ZM3.20682 6.77378C3.20682 3.8552 5.58132 1.48069 8.5001 1.48069C11.4187 1.48069 13.7932 3.8552 13.7932 6.77378V12.0671H3.20682V6.77378ZM2.97295 13.5478H14.027L14.8353 15.6257H2.16474L2.97295 13.5478ZM9.84371 17.2435C9.84371 17.9844 9.24083 18.5871 8.5001 18.5871C7.75917 18.5871 7.15629 17.9844 7.15629 17.2435V17.1064H9.84352V17.2435H9.84371Z"
                                              fill="#BB2749"></path>
                                    </svg>
                                </div>
                                <div class="cart-checkout__info-text">Вы можете воспользоваться средствами внутреннего
                                    счета выбрав способ оплаты
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? } ?>