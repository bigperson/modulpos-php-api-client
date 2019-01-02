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
}
