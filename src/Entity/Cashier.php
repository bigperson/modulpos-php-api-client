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

use Bigperson\ModulposApiClient\Contracts\ModulposCashierInterface;

/**
 * Class Order.
 */
class Cashier extends AbstractEntity implements ModulposCashierInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $inn;

    /**
     * @var string|null
     */
    protected $position;

    /**
     * Имя кассира.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * ИНН кассира (используется валидатор ИНН).
     *
     * @return string|null
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * Должность кассира (до 100 символов).
     *
     * @return string|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param null|string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param null|string $inn
     *
     * @return $this
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * @param $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
