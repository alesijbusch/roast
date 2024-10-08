<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>

<div class="cart-empty">
    <div class="cart-empty__title"><?=Loc::getMessage("EMPTY_BASKET_TITLE")?></div>
    <div class="cart-empty__descr"><?= Loc::getMessage('EMPTY_BASKET_TEXT') ?></div>
    <div class="cart-empty__link">
        <a class="btn btn--primary" href="<?=$arParams['EMPTY_BASKET_HINT_PATH']?>"><?= Loc::getMessage('EMPTY_BASKET_HINT_PATH') ?></a>
    </div>
</div>

