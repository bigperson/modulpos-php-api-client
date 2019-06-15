<?php
/**
 * This file is part of Pandora-alarm.ru package.
 *
 * @author Anton Kartsev <anton@alarmcrm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\ModulposApiClient\Contracts;

/**
 * Interface ModulposOrderInterface.
 */
interface ModulposOrderInterface
{
    /**
     * Уникальный номер документа чека
     * Определяется на отправляющей стороне, для исключения ситуации
     * с дублированием документов в случае потери ответа сервера.
     * Id должен быть достаточно большой строкой из букв и чисел
     * для сведения к минимуму возможности коллизий.
     *
     * @return string
     */
    public function getDocumentUuid();

    /**
     * Уникальный номер заказа в системе магазина.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Адрес электронной почты или номер телефона плательщика.
     *
     * @return string
     */
    public function getCustomerContact();

    /**
     * Тип операции
     * SALE - продажа
     * RETURN - возврат
     *
     * @return string
     */
    public function getTypeOperation();

    /**
     * Товары/услуги в чеке.
     *
     * @return array|ModulposOrderItemInterface[] массив объектов ModulposOrderItem
     */
    public function getItems();

    /**
     * Способы оплаты в чеке.
     *
     * @return array|ModulposPaymentItemInterface[] массив объектов ModulposPaymentItem
     */
    public function getPaymentItems();

    /**
     * Дата и время оплаты в формате DATE_RFC3339.
     *
     * @return string
     */
    public function getCheckoutDateTime();
}
