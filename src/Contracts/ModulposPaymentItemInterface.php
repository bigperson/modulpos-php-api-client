<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bigperson\ModulposApiClient\Contracts;

/**
 * Interface ModulposPaymentItemInterface
 *
 * @package Bigperson\ModulposApiClient\Contracts
 */
interface ModulposPaymentItemInterface
{
    /**
     * Тип оплаты
     * CARD - безналичная оплата
     * CASH - оплата наличными
     *
     * @return string
     */
    public function getType();

    /**
     * Сумма выбранного типа оплаты
     *
     * @return float
     */
    public function getSum();
}