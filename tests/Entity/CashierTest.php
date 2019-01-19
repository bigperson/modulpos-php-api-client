<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Order;

use Bigperson\ModulposApiClient\Entity\Cashier;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Tests\TestCase;

/**
 * Class CashierTest.
 */
class CashierTest extends TestCase
{
    private $cashierName = 'test name';
    private $inn = '2123213213';
    private $position = 'salesman';

    /**
     * @return void
     */
    public function testOrderCanBeCreated()
    {
        $order = new Cashier();
        $order->setName($this->cashierName);
        $order->setInn($this->inn);
        $order->setPosition($this->position);

        $this->assertEquals($order->getName(), $this->cashierName);
        $this->assertEquals($order->getInn(), $this->inn);
        $this->assertEquals($order->getPosition(), $this->position);
    }

    /**
     * @return void
     */
    public function testOrderCanBeCreatedByArray()
    {
        $cashier = Cashier::create([
            'name'         => $this->cashierName,
            'inn'          => $this->inn,
            'position'     => $this->position,
        ]);

        $this->assertEquals($cashier->getName(), $this->cashierName);
        $this->assertEquals($cashier->getInn(), $this->inn);
        $this->assertEquals($cashier->getPosition(), $this->position);
    }

    /**
     * @return void
     */
    public function testOrderCanNotBeCreatedByArray()
    {
        try {
            Cashier::create([
                'name'             => $this->cashierName,
                'inn'              => $this->inn,
                'position'         => $this->position,
                'methodNotAllowed' => 'methodNotAllowed',
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof MethodNotFound);
        }
    }
}
