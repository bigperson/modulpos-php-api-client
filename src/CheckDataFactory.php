<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigperson\ModulposApiClient;

use Bigperson\ModulposApiClient\Contracts\ModulposCashierInterface;
use Bigperson\ModulposApiClient\Contracts\ModulposOrderInterface;
use Bigperson\ModulposApiClient\Exceptions\ItemsNotFound;
use Bigperson\ModulposApiClient\Exceptions\RequiredParameterNotFound;

/**
 * Class CheckDataFactory.
 *
 * Фабрика преобразует объект интерфейса ModulposOrderInterface в массив
 * который можно использовать для отправки данных в API модулькассы
 */
class CheckDataFactory
{
    /**
     * @param ModulposOrderInterface $order
     * @param null $responseUrl URL для подтверждения успешной фискализации на стороне Интернет-магазина
     * @param bool $printReceipt Печатать ли бумажный чек на кассе при фискализации
     * @param ModulposCashierInterface $cashier Информация о кассире
     * @return array
     */
    public static function convertToArray(
        ModulposOrderInterface $order,
        $responseUrl = null,
        $printReceipt = false,
        $cashier = null
    ) {
        self::validate($order);

        $checkData = [
            'id'               => $order->getDocumentUuid(),
            'checkoutDateTime' => $order->getCheckoutDateTime(),
            'docNum'           => $order->getOrderId(),
            'docType'          => $order->getTypeOperation(),
            'printReceipt'     => $printReceipt,
            'responseURL'      => $responseUrl,
            'email'            => $order->getCustomerContact(),
        ];

        if ($cashier) {
            $checkData['cashierName'] = $cashier->getName();
            $checkData['cashierInn'] = $cashier->getInn();
            $checkData['cashierPosition'] = $cashier->getPosition();
        }

        foreach ($order->getItems() as $item) {
            /** @var \Bigperson\ModulposApiClient\Contracts\ModulposOrderItemInterface $item */
            $itemData = [
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getQuantity(),
                'vatTag'   => $item->getVatTag(),
            ];
            $checkData['inventPositions'][] = $itemData;
        }

        foreach ($order->getPaymentItems() as $paymentItem) {
            /** @var \Bigperson\ModulposApiClient\Contracts\ModulposPaymentItemInterface $paymentItem */
            $paymentItemData = [
                'paymentType' => $paymentItem->getType(),
                'sum'         => $paymentItem->getSum(),
            ];

            $checkData['moneyPositions'][] = $paymentItemData;
        }

        return $checkData;
    }

    /**
     * @param \Bigperson\ModulposApiClient\Contracts\ModulposOrderInterface $order
     *
     * @throws \Bigperson\ModulposApiClient\Exceptions\ItemsNotFound
     * @throws \Bigperson\ModulposApiClient\Exceptions\RequiredParameterNotFound
     */
    private static function validate(ModulposOrderInterface $order)
    {
        if (!$order->getDocumentUuid()) {
            throw new RequiredParameterNotFound('documentUuid is required');
        }

        if (!$order->getCheckoutDateTime()) {
            throw new RequiredParameterNotFound('checkoutDateTime is required');
        }

        if (!$order->getOrderId()) {
            throw new RequiredParameterNotFound('orderId is required');
        }

        if (!$order->getTypeOperation()) {
            throw new RequiredParameterNotFound('typeOperation is required');
        }

        if (!$order->getCustomerContact()) {
            throw new RequiredParameterNotFound('customerContact is required');
        }

        $items = $order->getItems();

        if (empty($items)) {
            throw new ItemsNotFound('orderItems is required');
        }

        foreach ($items as $item) {
            /** @var \Bigperson\ModulposApiClient\Contracts\ModulposOrderItemInterface $item */
            if (!$item->getPrice()) {
                throw new RequiredParameterNotFound('price in orderItem is required');
            }
            if (!$item->getVatTag()) {
                throw new RequiredParameterNotFound('vatTag in orderItem is required');
            }
            if (!$item->getQuantity()) {
                throw new RequiredParameterNotFound('quantity in orderItem is required');
            }
            if (!$item->getName()) {
                throw new RequiredParameterNotFound('name in orderItem is required');
            }
        }

        $paymentItems = $order->getPaymentItems();

        if (empty($paymentItems)) {
            throw new ItemsNotFound('paymentItems is required');
        }

        foreach ($paymentItems as $paymentItem) {
            /** @var \Bigperson\ModulposApiClient\Contracts\ModulposPaymentItemInterface $paymentItem */
            if (!$paymentItem->getType()) {
                throw new RequiredParameterNotFound('type in paymentItem is required');
            }
            if (!$paymentItem->getSum()) {
                throw new RequiredParameterNotFound('sum in paymentItem is required');
            }
        }
    }
}
