<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigperson\ModulposApiClient\Entity;

use Bigperson\ModulposApiClient\Contracts\ModulposOrderItemInterface;
use Bigperson\ModulposApiClient\Exceptions\PaymentMethodNotAllowed;
use Bigperson\ModulposApiClient\Exceptions\PaymentObjectNotAllowed;
use Bigperson\ModulposApiClient\Exceptions\VatTagNotAllowed;

/**
 * Class OrderItem.
 */
class OrderItem extends AbstractEntity implements ModulposOrderItemInterface
{
    /**
     * @var float
     */
    protected $price;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $vatTag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $paymentObject = 'commodity';

    /**
     * @var string
     */
    protected $paymentMethod = 'full_payment';

    /**
     * @var array
     */
    protected $allowedVatTags = [
        self::VAT_NO,
        self::VAT_0,
        self::VAT_10,
        self::VAT_18,
        self::VAT_20,
        self::VAT_10_110,
        self::VAT_18_118,
        self::VAT_20_120,
    ];

    /**
     * @var array
     */
    protected $allowedPaymentObject = [
        'commodity',
        'excise',
        'job',
        'service',
        'gambling_bet',
        'gambling_prize',
        'lottery',
        'lottery_prize',
        'intellectual_activity',
        'payment',
        'agent_commission',
        'composite',
        'another',
    ];

    /**
     * @var array
     */
    protected $allowedPaymentMethod = [
        'full_prepayment',
        'prepayment',
        'advance',
        'full_payment',
        'partial_payment',
        'credit',
        'credit_payment',
    ];

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getVatTag()
    {
        return $this->vatTag;
    }

    /**
     * @param int $vatTag
     *
     * @throws \Bigperson\ModulposApiClient\Exceptions\VatTagNotAllowed
     */
    public function setVatTag($vatTag)
    {
        if (!in_array($vatTag, $this->allowedVatTags)) {
            throw new VatTagNotAllowed("$vatTag is not allowed");
        }

        $this->vatTag = $vatTag;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPaymentObject()
    {
        return $this->paymentObject;
    }

    /**
     * @param string $paymentObject
     */
    public function setPaymentObject($paymentObject)
    {
        if (!in_array($paymentObject, $this->allowedPaymentObject)) {
            throw new PaymentObjectNotAllowed("$paymentObject is not allowed");
        }

        $this->paymentObject = $paymentObject;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        if (!in_array($paymentMethod, $this->allowedPaymentMethod)) {
            throw new PaymentMethodNotAllowed("$paymentMethod is not allowed");
        }

        $this->paymentMethod = $paymentMethod;
    }
}
