<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y")
{
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}

?>

<? if (!empty($arResult["ORDER"]) || $_REQUEST['ORDER_ID'] > 0): ?>
	<?Bitrix\Main\Page\Asset::getInstance()->addCss(ASSET_TEMPLATE_PATH . "/components-template/404/style.css");?>
	<div class="pnf">
		<div class="pnf-inner">
			<p class="order-status__title">
				Спасибо, ваш заказ принят и находится в обработке.
			</p>
			<p>
				Номер заказа <span class="order-status__number">№<?=htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])?></span>
			</p>
			<p>
				Вы можете следить за выполнением своего заказа в <a href="/personal/">Персональном разделе сайта</a>.
			</p>
			<p>
				Обратите внимание, что для входа в этот раздел вам необходимо будет ввести логин и пароль пользователя сайта.
			</p>

			<p><? if ( !empty($arResult['PAYMENT']) ) {
				foreach ( $arResult['PAYMENT'] as $payment ) { 
					if ( !empty($arResult['PAY_SYSTEM_LIST']) && array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST']) ) {
						$arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];
						if ( in_array($arPaySystem['CODE'], ['bepaid', 'erip', 'oplati']) ) {?>
							Обратите внимание: при онлайн-оплате у вас <b>есть 3 часа на завершение платежа.</b> Если вы не успеете, заказ автоматически <b>отменится.</b>
						<? } ?>
					<? } ?>
				<? } ?>
			<? } ?></p>
			
			<div class="pnf-btns"><a class="btn btn-lg" href="/catalog/">Каталог товаров</a><a class="btn btn-lg btn-second" href="/personal/">Личный кабинет</a></div>
			<?if ( $arResult['ORDER']["IS_ALLOW_PAY"] === 'Y' ) {		
				if ( !empty($arResult['PAYMENT']) ) {
					foreach ( $arResult['PAYMENT'] as $payment ) {
						if ($payment["PAID"] != 'Y')
						{
							if ( !empty($arResult['PAY_SYSTEM_LIST']) && array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST']) ) {
								$arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];

								if ( $arPaySystem['BUFFERED_OUTPUT'] && $arPaySystem['CODE'] == 'bepaid') { ?>
									<div class='payment-form hidden'>
										<?=$arPaySystem['BUFFERED_OUTPUT'];?>
									</div>
								<? } ?>

								<?if ( $arPaySystem['BUFFERED_OUTPUT'] && $arPaySystem['CODE'] == 'erip') { ?>
									<?=$arPaySystem['BUFFERED_OUTPUT'];?>
								<? } ?>

								<?if ( $arPaySystem['BUFFERED_OUTPUT'] && $arPaySystem['CODE'] == 'oplati') { ?>
									<?=$arPaySystem['BUFFERED_OUTPUT'];?>
								<? } ?>
							<? } ?>
						<? } ?>
					<? } ?>
					<script>
						document.addEventListener("DOMContentLoaded", function(){
							let paymentForm = document.querySelector('.payment-form form');
							paymentForm.submit();
						});
					</script>
				<?}?>
			<?}?>
		</div>
	</div>
<? else: ?>

	<b><?=Loc::getMessage("SOA_ERROR_ORDER")?></b>
	<br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST", ["#ORDER_ID#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])])?>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>

<? endif ?>

