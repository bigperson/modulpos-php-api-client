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

    public function testOrderCanBeCreated(): void
    {
        $order = new Cashier();
        $order->setName($this->cashierName);
        $order->setInn($this->inn);
        $order->setPosition($this->position);

        $this->assertEquals($order->getName(), $this->cashierName);
        $this->assertEquals($order->getInn(), $this->inn);
        $this->assertEquals($order->getPosition(), $this->position);
    }

    public function testOrderCanBeCreatedByArray(): void
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

    public function testOrderCanNotBeCreatedByArray(): void
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
