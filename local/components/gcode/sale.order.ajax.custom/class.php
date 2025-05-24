<?php
use Bitrix\Main\Diag\Debug;

use Bitrix\Main;
use Bitrix\Catalog;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Result;
use Bitrix\Sale\Shipment;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("sale")) {
    ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));

    return;
}

CBitrixComponent::includeComponentClass("bitrix:sale.order.ajax");

class SlamSaleOrderAjax extends SaleOrderAjax
{
    protected function getUserId()
    {
        global $USER;

        return $USER instanceof CUser ? $USER->GetID() : null;
    }

    // legacy method

    /**
     * Взято из класса CBitrixBasketComponent
     * @param $postList
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectNotFoundException
     */
    public function recalculateBasket($postList)
    {

        if (!empty($postList)) {
            $itemsActionData = $this->extractItemsActionData($postList);

            if (!empty($itemsActionData)) {
                $itemsRatioData = $this->getBasketItemsRatios($itemsActionData);
                $basket = $this->getBasketStorage()->getBasket();

                foreach ($itemsActionData as $id => $itemActionData) {
                    $item = $basket->getItemByBasketCode($id);

                    if ($item) {
                        if (!empty($itemActionData['POST_DELETE'])) {
                            $res = $item->delete();
                            if ($res->isSuccess()) {
                                // compatibility
                                $userId = $this->getUserId();

                                if ($item->getField('SUBSCRIBE') === 'Y' && is_array($_SESSION['NOTIFY_PRODUCT'][$userId])) {
                                    unset($_SESSION['NOTIFY_PRODUCT'][$userId][$item->getProductId()]);
                                }

                                $_SESSION['SALE_BASKET_NUM_PRODUCTS'][$this->getSiteId()]--;
                            } else {
                                $this->addError($res->getErrors(), $item->getId());
                            }
                        } elseif ($item->canBuy()) {
                            if (isset($itemActionData['POST_QUANTITY'])
                                && !empty($itemsRatioData[$id])
                                && $item->getQuantity() != $itemActionData['POST_QUANTITY']
                            ) {
                                $this->processChangeQuantity($itemsRatioData[$id], $itemActionData['POST_QUANTITY']);
                            }
                        }
                    }
                }

                /**
                 * Расчет кол-ва в соответствии с RATIO
                 */
                $this->refreshAndCorrectRatio();

                $basket->save();
            }
        }
    }

    /**
     * Взято из класса CBitrixBasketComponent
     * @param $itemRatioData
     * @param $quantity
     * @return void
     */
    protected function processChangeQuantity($itemRatioData, $quantity)
    {
        $res = $this->checkQuantity($itemRatioData, $quantity);
        if (!empty($res['ERRORS'])) {
            $this->addError($res['ERRORS'], $itemRatioData['ID']);
        }
    }

    /**
     * Взято из класса CBitrixBasketComponent
     * @param $basketItemData
     * @param $desiredQuantity
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    public function checkQuantity($basketItemData, $desiredQuantity)
    {
        $result = [];

        if ((
                isset($basketItemData['MEASURE_RATIO'])
                && (float)$basketItemData['MEASURE_RATIO'] > 0
                && (float)$basketItemData['MEASURE_RATIO'] != (int)$basketItemData['MEASURE_RATIO']
            )
        ) {
            $isFloatQuantity = true;
        } else {
            $isFloatQuantity = false;
        }

        $quantity = $isFloatQuantity ? (float)$desiredQuantity : (int)$desiredQuantity;

        if ($basketItemData['QUANTITY'] != $quantity) {
            $basket = $this->getBasketStorage()->getBasket();
            $basketItem = $basket->getItemByBasketCode($basketItemData['ID']);
            $res = $basketItem->setField('QUANTITY', $desiredQuantity);

            if (!$res->isSuccess()) {
                $this->addError($res->getErrorMessages(), "PRODUCT_" . $basketItemData['ID']);
            }
        }

        return $result;
    }

    /**
     * Взято из класса CBitrixBasketComponent
     * @param $actionData
     * @return array|mixed
     * @throws Main\ArgumentNullException
     */
    protected function getBasketItemsRatios($actionData)
    {
        $ratioData = [];

        if (!empty($actionData) && is_array($actionData)) {
            $basket = $this->getBasketStorage()->getBasket();

            foreach ($actionData as $id => $data) {
                if (!empty($data['POST_QUANTITY'])) {
                    $basketItem = $basket->getItemByBasketCode($id);
                    if ($basketItem) {
                        $ratioData[$id] = $basketItem->getFieldValues();
                    }
                }
            }

            if (!empty($ratioData)) {
                $ratioData = getRatio($ratioData);
            }
        }

        return $ratioData;
    }

    /**
     * Взято из класса CBitrixBasketComponent
     * корректировка кол-ва товара в соответствии с RATIO
     * @return array
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     */
    protected function refreshAndCorrectRatio()
    {
        //$basketRefreshed = false;
        $changedItems = [];

        $basket = $this->getBasketStorage()->getBasket();

        $actualQuantityList = $this->getActualQuantityList($basket);


        $refreshResult = $this->refreshBasket($basket);

        if ($refreshResult->isSuccess()) {
            $items = $refreshResult->get('CHANGED_BASKET_ITEMS');
            if (!empty($items)) {
                $changedItems = array_merge($changedItems, $items);
            }
        }

        $basketRefreshed = true;


        if ($this->arParams['CORRECT_RATIO'] === 'Y') {
            $ratioResult = Sale\BasketComponentHelper::correctQuantityRatio($basket);

            $items = $ratioResult->get('CHANGED_BASKET_ITEMS');
            if (!empty($items)) {
                $changedItems = array_merge($changedItems, $items);
            }
        }


        $this->checkQuantityList($basket, $actualQuantityList);

        return [$basketRefreshed, $changedItems];
    }

    /**
     * Взято из класса CBitrixBasketComponent
     * @param Basket $basket
     * @return Sale\Result
     */
    protected function refreshBasket(Sale\Basket $basket)
    {
        $refreshStrategy = Basket\RefreshFactory::create(Basket\RefreshFactory::TYPE_FULL);

        $result = $basket->refresh($refreshStrategy);
        if (!$result->isSuccess()) {
            $this->addError($result->getErrors());
        }

        return $result;
    }


    /**
     * Взято из класса CBitrixBasketComponent
     * @param $basket
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    protected function getActualQuantityList($basket)
    {
        $quantityList = [];

        if (!$basket->isEmpty()) {
            /** @var Sale\BasketItemBase $basketItem */
            foreach ($basket as $basketItem) {
                if ($basketItem->canBuy() && !$basketItem->isDelay()) {
                    $quantityList[$basketItem->getBasketCode()] = $basketItem->getQuantity();
                }
            }
        }

        return $quantityList;
    }


    /**
     * Взято из класса CBitrixBasketComponent
     * @param $basket
     * @param $compareList
     * @return void
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    protected function checkQuantityList($basket, $compareList)
    {
        $actualQuantityList = $this->getActualQuantityList($basket);

        foreach ($actualQuantityList as $basketCode => $itemQuantity) {
            if (!isset($compareList[$basketCode]) || $itemQuantity != $compareList[$basketCode]) {
                $this->addError(Loc::getMessage('SBB_PRODUCT_QUANTITY_CHANGED'), 'PRODUCT_' . $basketCode);
            }
        }
    }


    /**
     * Взято из класса CBitrixBasketComponent
     * @param $postList
     * @return array
     */
    protected function extractItemsActionData($postList)
    {

        $itemsData = [];

        foreach ($postList as $key => $value) {
            if (mb_strpos($key, 'QUANTITY_') !== false) {
                $id = (int)mb_substr($key, 9);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_QUANTITY'] = $value;
            } elseif (mb_strpos($key, 'DELETE_') !== false) {
                $id = (int)mb_substr($key, 7);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_DELETE'] = $value === 'Y';
            } elseif (mb_strpos($key, 'RESTORE_') !== false) {
                $id = (int)mb_substr($key, 8);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_RESTORE'] = $value;
            } elseif (mb_strpos($key, 'DELAY_') !== false) {
                $id = (int)mb_substr($key, 6);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_DELAY'] = $value === 'Y' ? 'Y' : 'N';
            } elseif (mb_strpos($key, 'MERGE_OFFER_') !== false) {
                $id = (int)mb_substr($key, 12);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_MERGE_OFFER'] = $value === 'Y';
            } elseif (mb_strpos($key, 'OFFER_') !== false) {
                $id = (int)mb_substr($key, 6);

                if (!isset($itemsData[$id])) {
                    $itemsData[$id] = [];
                }

                $itemsData[$id]['POST_OFFER'] = $value;
            }
        }

        return $itemsData;
    }


    /**
     * Метод для изменения списка товаров
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectNotFoundException
     */
    protected function basketItemsAction()
    {
        global $USER;

        $error = false;
        $this->request->set($this->request->get('order'));


        if ($this->checkSession) {
            $this->recalculateBasket($this->request->toArray());


            $this->order = $this->createOrder($USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID());
            $this->prepareResultArray();

            self::scaleImages($this->arResult['JS_DATA'], $this->arParams['SERVICES_IMAGES_SCALING']);
        } else {
            $error = Loc::getMessage('SESSID_ERROR');
        }

        $this->showAjaxAnswer([
            'order' => $this->arResult['JS_DATA'],
            'locations' => $this->arResult['LOCATIONS'],
            'error' => $error,
        ]);
    }

	protected function changeBasketPropertyAction(){
		global $USER;

        $error = false;
        $order = $this->request->get('order');
		$basketProperty = $order['basket_property'];

		if($basketProperty['id'] && $basketProperty['value'] && $basketProperty['code']) {
			$basket = $this->getBasketStorage()->getBasket();
			$basketItem = $basket->getItemByBasketCode($basketProperty['id']);
			$basketPropertyCollection = $basketItem->getPropertyCollection();
			foreach ($basketPropertyCollection as $propertyItem) {
				if ($propertyItem->getField('CODE') == $basketProperty['code']) {
					$propertyItem->setField('VALUE', $basketProperty['value']);
					break;
				}
			}
			$basketPropertyCollection->save();
		}

		if ($this->checkSession) {
            $this->recalculateBasket($this->request->toArray());


            $this->order = $this->createOrder($USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID());
            $this->prepareResultArray();

            self::scaleImages($this->arResult['JS_DATA'], $this->arParams['SERVICES_IMAGES_SCALING']);
        } else {
            $error = Loc::getMessage('SESSID_ERROR');
        }

        $this->showAjaxAnswer([
            'order' => $this->arResult['JS_DATA'],
            'locations' => $this->arResult['LOCATIONS'],
            'error' => $error,
        ]);
	}
    /**
     * Prepares $this->arResult
     * Execution of 'OnSaleComponentOrderOneStepProcess' event
     */
    protected function prepareResultArray()
    {
        $this->initGrid();
        $this->obtainBasket();

        // Добавляем AVAILABLE_QUANTITY для каунтера макс кол-ва
        $this->getAvailableQuantity();
        $this->obtainPropertiesForIbElements();

        if ($this->arParams['COMPATIBLE_MODE'] == 'Y') {
            $this->obtainFormattedProperties();
        }


        $this->obtainDelivery();
        $this->obtainPaySystem();
        $this->obtainTaxes();
        $this->obtainTotal();

        if ($this->arParams['USER_CONSENT'] === 'Y') {
            $this->obtainUserConsentInfo();
        }

        $this->getJsDataResult();

        if ($this->arParams['COMPATIBLE_MODE'] == 'Y') {
            $this->obtainRelatedProperties();
            $this->makeResultCompatible();
            $this->makeOrderDataArray();
        }

        $this->arResult['USER_VALS'] = $this->arUserResult;
        $this->executeEvent('OnSaleComponentOrderOneStepProcess', $this->order);
        $this->arResult['USER_VALS'] = $this->arUserResult;

        //try to avoid use "executeEvent" methods and use new events like this
        foreach (GetModuleEvents("sale", 'OnSaleComponentOrderResultPrepared', true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, [$this->order, &$this->arUserResult, $this->request, &$this->arParams, &$this->arResult]);
        }
    }

    public function getAvailableQuantity()
    {
        $arResult =& $this->arResult;

        if (empty($arResult["BASKET_ITEMS"]) || !is_array($arResult["BASKET_ITEMS"])) {
            return [];
        }

        // добавляем MEASURE_RATIO (шаг для каунтера)
        $arResult["BASKET_ITEMS"] = getRatio($arResult["BASKET_ITEMS"]);


        $elementIds = [];
        $productMap = [];

        foreach ($arResult["BASKET_ITEMS"] as $key => $item) {
            $elementIds[$item['PRODUCT_ID']] = $item['PRODUCT_ID'];

            if (!isset($productMap[$item['PRODUCT_ID']])) {
                $productMap[$item['PRODUCT_ID']] = [];
            }

            $productMap[$item['PRODUCT_ID']][] = $key;
        }

        unset($key, $item);

        if (!empty($elementIds)) {
            sort($elementIds);
            $productIterator = Catalog\ProductTable::getList([
                'select' => ['ID', 'QUANTITY', 'QUANTITY_TRACE', 'CAN_BUY_ZERO'],
                'filter' => ['@ID' => $elementIds],
            ]);
            while ($product = $productIterator->fetch()) {
                if (!isset($productMap[$product['ID']])) {
                    continue;
                }

                $check = ($product['QUANTITY_TRACE'] == 'Y' && $product['CAN_BUY_ZERO'] == 'N' ? 'Y' : 'N');
                foreach ($productMap[$product['ID']] as $key) {
                    $arResult["BASKET_ITEMS"][$key]['AVAILABLE_QUANTITY'] = $product['QUANTITY'];
                    $arResult["BASKET_ITEMS"][$key]['CHECK_MAX_QUANTITY'] = $check;
                }

                unset($key, $check);
            }

            unset($product, $productIterator);
        }

        unset($productMap, $elementIds);
    }

	protected function initLastOrderData(Order $order)
	{
		global $USER;

		if (
			($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			&& $this->arUserResult['USE_PRELOAD']
			&& $USER->IsAuthorized()
		)
		{
			$showData = [];
			$lastOrderData = $this->getLastOrderData($order);

			if (!empty($lastOrderData))
			{
				if (!empty($lastOrderData['PERSON_TYPE_ID']))
					$this->arUserResult['PERSON_TYPE_ID'] = $showData['PERSON_TYPE_ID'] = $lastOrderData['PERSON_TYPE_ID'];

				if (!empty($lastOrderData['PAY_CURRENT_ACCOUNT']))
					$this->arUserResult['PAY_CURRENT_ACCOUNT'] = $showData['PAY_CURRENT_ACCOUNT'] = 'N';//$lastOrderData['PAY_CURRENT_ACCOUNT'];

				if (!empty($lastOrderData['PAY_SYSTEM_ID']))
					$this->arUserResult['PAY_SYSTEM_ID'] = $showData['PAY_SYSTEM_ID'] = $lastOrderData['PAY_SYSTEM_ID'];

				if (!empty($lastOrderData['DELIVERY_ID']))
					$this->arUserResult['DELIVERY_ID'] = $showData['DELIVERY_ID'] = $lastOrderData['DELIVERY_ID'];

				if (!empty($lastOrderData['DELIVERY_EXTRA_SERVICES']))
					$this->arUserResult['DELIVERY_EXTRA_SERVICES'] = $showData['DELIVERY_EXTRA_SERVICES'] = $lastOrderData['DELIVERY_EXTRA_SERVICES'];

				if (!empty($lastOrderData['BUYER_STORE']))
				{
					$this->arUserResult['BUYER_STORE'] = $lastOrderData['BUYER_STORE'];
					$showData['BUYER_STORE'] = $lastOrderData['BUYER_STORE'];
				}

				$this->arUserResult['LAST_ORDER_DATA'] = $showData;
			}
		}
	}

	/**
	 * Initialization of inner/external payment objects with first/selected pay system services.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initPayment(Order $order)
	{
		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order);

		if ($sumToSpend > 0)
		{
			$innerPayment = $this->getInnerPayment($order);
			if (!empty($innerPayment))
			{
				if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y')
				{
					$innerPayment->setField('SUM', $sumToSpend);
				}
				else
				{
					$innerPayment->delete();
					$innerPayment = null;
				}

				$this->arPaySystemServiceAll = $this->arActivePaySystems = $innerPaySystemList;
			}
		}

		$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
		$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

		$paymentCollection = $order->getPaymentCollection();
		$remainingSum = $order->getPrice() - $paymentCollection->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			/** @var Payment $extPayment */
			$extPayment = $paymentCollection->createItem();
			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);

			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $this->arActivePaySystems = $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				// $selectedPaySystem = current($this->arPaySystemServiceAll);

				if (!empty($selectedPaySystem) && $extPaySystemId != 0)
				{
					$this->addWarning(Loc::getMessage('PAY_SYSTEM_CHANGE_WARNING'), self::PAY_SYSTEM_BLOCK);
				}
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}
		}

		if (empty($this->arPaySystemServiceAll))
		{
			$this->addError(Loc::getMessage('SOA_ERROR_PAY_SYSTEM'), self::PAY_SYSTEM_BLOCK);
		}

		if (!empty($this->arUserResult['PREPAYMENT_MODE']))
		{
			$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
		}
	}

	/**
	 * Recalculates payment prices which could change due to shipment/discounts.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function recalculatePayment(Order $order)
	{
		$res = $order->getShipmentCollection()->calculateDelivery();

		if (!$res->isSuccess())
		{
			$shipment = $this->getCurrentShipment($order);

			if (!empty($shipment))
			{
				$errMessages = '';
				$errors = $res->getErrorMessages();

				if (!empty($errors))
				{
					foreach ($errors as $message)
					{
						$errMessages .= $message.'<br />';
					}
				}
				else
				{
					$errMessages = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
				}

				$r = new Result();
				$r->addError(new Sale\ResultWarning(
					$errMessages,
					'SALE_DELIVERY_CALCULATE_ERROR'
				));

				Sale\EntityMarker::addMarker($order, $shipment, $r);
				$shipment->setField('MARKED', 'Y');
			}
		}

		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order, true);

		$innerPayment = $this->getInnerPayment($order);
		if (!empty($innerPayment))
		{
			if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y' && $sumToSpend > 0)
			{
				$innerPayment->setField('SUM', $sumToSpend);
			}
			else
			{
				$innerPayment->delete();
				$innerPayment = null;
			}

			if ($sumToSpend > 0)
			{
				$this->arPaySystemServiceAll = $innerPaySystemList;
				$this->arActivePaySystems += $innerPaySystemList;
			}
		}

		/** @var Payment $innerPayment */
		$innerPayment = $this->getInnerPayment($order);
		/** @var Payment $extPayment */
		$extPayment = $this->getExternalPayment($order);

		$remainingSum = empty($innerPayment) ? $order->getPrice() : $order->getPrice() - $innerPayment->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			$paymentCollection = $order->getPaymentCollection();
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

			if (empty($extPayment))
			{
				$extPayment = $paymentCollection->createItem();
			}

			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);
			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $extPaySystemList;
			$this->arActivePaySystems += $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);

						if ($order->getPrice() - $paymentCollection->getSum() > 0)
						{
							$this->addWarning(Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'), self::PAY_SYSTEM_BLOCK);

							$r = new Result();
							$r->addError(new Sale\ResultWarning(
								Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'),
								'SALE_INNER_PAYMENT_BALANCE_ERROR'
							));

							Sale\EntityMarker::addMarker($order, $innerPayment, $r);
							$innerPayment->setField('MARKED', 'Y');
						}
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				// $selectedPaySystem = current($this->arPaySystemServiceAll);

				if (!empty($selectedPaySystem) && $extPaySystemId != 0)
				{
					$this->addWarning(Loc::getMessage('PAY_SYSTEM_CHANGE_WARNING'), self::PAY_SYSTEM_BLOCK);
				}
			}

			if (!array_key_exists((int)$selectedPaySystem['ID'], $this->arPaySystemServiceAll))
			{
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::PAY_SYSTEM_BLOCK);
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::DELIVERY_BLOCK);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$codSum = 0;
					$service = PaySystem\Manager::getObjectById($selectedPaySystem['ID']);
					if ($service !== null)
					{
						$codSum = $service->getPaymentPrice($extPayment);
					}

					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
						'PRICE_COD' => $codSum,
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}

			if (!empty($this->arUserResult['PREPAYMENT_MODE']))
			{
				$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
			}
		}

		if (!empty($innerPayment) && !empty($extPayment) && $remainingSum == 0)
		{
			$extPayment->delete();
			$extPayment = null;
		}
	}

	/**
	 * Calculates all available deliveries for order object.
	 * Uses cloned order not to harm real order.
	 * Execution of 'OnSaleComponentOrderDeliveriesCalculated' event
	 *
	 * @param Order $order
	 * @throws Main\NotSupportedException
	 */
	protected function calculateDeliveries(Order $order)
	{
		$this->arResult['DELIVERY'] = [];
		$problemDeliveries = [];

		if (!empty($this->arDeliveryServiceAll))
		{
			/** @var Order $orderClone */
			$orderClone = null;
			$anotherDeliveryCalculated = false;
			/** @var Shipment $shipment */
			$shipment = $this->getCurrentShipment($order);

			foreach ($this->arDeliveryServiceAll as $deliveryId => $deliveryObj)
			{
				$calcResult = false;
				$calcOrder = false;
				$arDelivery = [];

				if ((int)$shipment->getDeliveryId() === $deliveryId)
				{
					$arDelivery['CHECKED'] = 'Y';
					$mustBeCalculated = true;
					$calcResult = $deliveryObj->calculate($shipment);
					$calcOrder = $order;
				}
				else
				{
					$mustBeCalculated = $this->arParams['DELIVERY_NO_AJAX'] === 'Y'
						|| ($this->arParams['DELIVERY_NO_AJAX'] === 'H' && $deliveryObj->isCalculatePriceImmediately());

					if ($mustBeCalculated)
					{
						$anotherDeliveryCalculated = true;

						if (empty($orderClone))
						{
							$orderClone = $this->getOrderClone($order);
						}

						$orderClone->isStartField();

						$clonedShipment = $this->getCurrentShipment($orderClone);
						$clonedShipment->setField('DELIVERY_ID', $deliveryId);

						$calculationResult = $orderClone->getShipmentCollection()->calculateDelivery();
						if ($calculationResult->isSuccess())
						{
							$calcDeliveries = $calculationResult->get('CALCULATED_DELIVERIES');
							$calcResult = reset($calcDeliveries);
						}
						else
						{
							$calcResult = new Delivery\CalculationResult();
							$calcResult->addErrors($calculationResult->getErrors());
						}

						$orderClone->doFinalAction(true);

						$calcOrder = $orderClone;
					}
				}

				if ($mustBeCalculated)
				{
					if ($calcResult->isSuccess())
					{
						$arDelivery['PRICE'] = Sale\PriceMaths::roundPrecision($calcResult->getPrice());
						$arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['PRICE'], $calcOrder->getCurrency());

						$currentCalcDeliveryPrice = Sale\PriceMaths::roundPrecision($calcOrder->getDeliveryPrice());
						if ($currentCalcDeliveryPrice >= 0 && $arDelivery['PRICE'] != $currentCalcDeliveryPrice)
						{
							$arDelivery['DELIVERY_DISCOUNT_PRICE'] = $currentCalcDeliveryPrice;
							$arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['DELIVERY_DISCOUNT_PRICE'], $calcOrder->getCurrency());
						}

						if ($calcResult->getPeriodDescription() <> '')
						{
							$arDelivery['PERIOD_TEXT'] = $calcResult->getPeriodDescription();
						}
					}
					else
					{
						$errorMessages = $calcResult->getErrorMessages();
						if (!empty($errorMessages))
						{
							$arDelivery['CALCULATE_ERRORS'] = implode('<br>', $errorMessages);
						}
						else
						{
							$arDelivery['CALCULATE_ERRORS'] = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
						}
						if ($arDelivery['CHECKED'] !== 'Y')
						{
							if ($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'N')
							{
								unset($this->arDeliveryServiceAll[$deliveryId]);
								continue;
							}
							elseif ($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'L')
							{
								$problemDeliveries[$deliveryId] = $arDelivery;
								continue;
							}
						}
					}

					$arDelivery['CALCULATE_DESCRIPTION'] = $calcResult->getDescription();
				}
                if(($deliveryObj->getConfigValues()['MAIN']['TO_TERMINAL_EUROPOST']??'N') == 'Y'){
                    $arDelivery['TO_TERMINAL_EUROPOST'] = 'Y';
                }
                $this->arResult['DELIVERY'][$deliveryId] = $arDelivery;
			}

			// for discounts: last delivery calculation need to be on real order with selected delivery
			if ($anotherDeliveryCalculated)
			{
				$order->doFinalAction(true);
			}
		}
		if (!empty($problemDeliveries))
		{
			$this->arResult['DELIVERY'] += $problemDeliveries;
		}

		$eventParameters = [
			$order, &$this->arUserResult, $this->request,
			&$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll,
		];
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderDeliveriesCalculated', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, $eventParameters);
		}
	}

    public function executeComponent()
    {
        global $APPLICATION;

        $this->setFrameMode(false);
        $this->context = Main\Application::getInstance()->getContext();
        $this->checkSession = $this->arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid();
        $this->isRequestViaAjax = $this->request->isPost() && $this->request->get('via_ajax') == 'Y';
        $isAjaxRequest = $this->request["is_ajax_post"] == "Y";

        if ($isAjaxRequest) {
            $APPLICATION->RestartBuffer();
        }

        $logger = getLogger();

        $postList = $this->request->getPostList()->toArray();
        if ($postList['soa-action'] === 'saveOrderAjax') {
            $logger->debug('ORDER', [
                'REQUEST' => $postList,
            ]);
        }

        try {
            $this->action = $this->prepareAction();
            Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
            $this->doAction($this->action);
            Sale\Compatible\DiscountCompatibility::revertUsageCompatible();

            //is included in all cases for old template
            $this->includeComponentTemplate();
        } catch (\Throwable $exception) {
            $logger->error('ORDER', [
                'ERROR' => $exception->getMessage(),
                'CODE' => $exception->getCode(),
                'FILE' => $exception->getFile(),
                'LINE' => $exception->getLine(),
                'TRACE' => $exception->getTraceAsString(),
                'REQUEST' => $postList,
            ]);
            throw $exception;
        }

        if ($isAjaxRequest) {
            $APPLICATION->FinalActions();
            die();
        }
    }
}
