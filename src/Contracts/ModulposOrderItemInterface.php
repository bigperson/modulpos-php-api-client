<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarmcrm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\ModulposApiClient\Contracts;

/**
 * Interface ModulposOrderItemInterface.
 */
interface ModulposOrderItemInterface
{
    const VAT_NO = 1105; // НДС не облагается
    const VAT_0 = 1104; // НДС 0%
    const VAT_10 = 1103; // НДС 10%
    const VAT_18 = 1102; // НДС 18% (с 1-го января 2019 - 20%), сохранено для обратной совместимости
    const VAT_20 = 1102; // НДС 20%
    const VAT_10_110 = 1107; // НДС с рассч. ставкой 10%
    const VAT_18_118 = 1106; // НДС с рассч. ставкой 18% (с 1-го января 2019 - 20%), сохранено для обратной совместимости
    const VAT_20_120 = 1106; // НДС с рассч. ставкой 20%

    /**
     * Цена товара с учётом всех скидок и наценок.
     *
     * @return float
     */
    public function getPrice();

    /**
     * Количество товара.
     *
     * @return float
     */
    public function getQuantity();

    /**
     * Ставка НДС.
     *
     * См. константы VAT_*.
     *
     * @return int
     */
    public function getVatTag();

    /**
     * Название товара. 128 символов в UTF-8.
     *
     * @return string
     */
    public function getName();

    /**
     * Предмет расчета.
     *
     * @return string
     */
    public function getPaymentObject();

    /**
     * Признак расчета.
     *
     * @return string
     */
    public function getPaymentMethod();
}
