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

namespace Bigperson\ModulposApiClient\Entity;

use Bigperson\ModulposApiClient\Contracts\ModulposPaymentItemInterface;
use Bigperson\ModulposApiClient\Exceptions\TypeOperationsNotAllowed;

/**
 * Class PaymentItem.
 */
class PaymentItem extends AbstractEntity implements ModulposPaymentItemInterface
{
    /**
     * @var array
     */
    protected $allowedTypes = [
        'CARD',
        'CASH',
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var float
     */
    protected $sum;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @throws \Bigperson\ModulposApiClient\Exceptions\TypeOperationsNotAllowed
     */
    public function setType($type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new TypeOperationsNotAllowed("$type is not allowed");
        }

        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param float $sum
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
    }
}
