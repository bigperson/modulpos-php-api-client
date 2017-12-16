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

use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Bigperson\ModulposApiClient\Exceptions\VatTagNotAllowed;
use Tests\TestCase;

/**
 * Class OrderItemTest.
 */
class OrderItemTest extends TestCase
{
    private $price;
    private $quantity;
    private $vatTag;
    private $name;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->price = 32.21;
        $this->quantity = rand(1, 10);
        $this->vatTag = OrderItem::VAT_NO;
        $this->name = 'Test product';

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testOrderItemCanBeCreated()
    {
        $order = new OrderItem();
        $order->setPrice($this->price);
        $order->setName($this->name);
        $order->setQuantity($this->quantity);
        $order->setVatTag($this->vatTag);

        $this->assertEquals($order->getName(), $this->name);
        $this->assertEquals($order->getPrice(), $this->price);
        $this->assertEquals($order->getQuantity(), $this->quantity);
        $this->assertEquals($order->getVatTag(), $this->vatTag);
    }

    /**
     * @return void
     */
    public function testOrderItemCanBeCreatedByArray()
    {
        $order = OrderItem::create([
            'price'        => $this->price,
            'name'         => $this->name,
            'quantity'     => $this->quantity,
            'vatTag'       => $this->vatTag,
        ]);

        $this->assertEquals($order->getName(), $this->name);
        $this->assertEquals($order->getPrice(), $this->price);
        $this->assertEquals($order->getQuantity(), $this->quantity);
        $this->assertEquals($order->getVatTag(), $this->vatTag);
    }

    /**
     * @return void
     */
    public function testOrderItemCanNotBeCreatedByArray()
    {
        try {
            $order = OrderItem::create([
                'price'            => $this->price,
                'name'             => $this->name,
                'quantity'         => $this->quantity,
                'vatTag'           => $this->vatTag,
                'methodNotAllowed' => 'methodNotAllowed',
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof MethodNotFound);
        }
    }

    /**
     * @return void
     */
    public function testOrderItemCanNotSetVatTag()
    {
        try {
            $order = new OrderItem();
            $order->setVatTag('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof VatTagNotAllowed);
        }
    }
}
