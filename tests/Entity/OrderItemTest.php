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

use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Bigperson\ModulposApiClient\Exceptions\PaymentMethodNotAllowed;
use Bigperson\ModulposApiClient\Exceptions\PaymentObjectNotAllowed;
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
    private $paymentMethod;
    private $paymentObject;
    
    public function setUp(): void
    {
        $this->price = 32.21;
        $this->quantity = rand(1, 10);
        $this->vatTag = OrderItem::VAT_NO;
        $this->name = 'Test product';
        $this->paymentMethod = 'full_prepayment';
        $this->paymentObject = 'commodity';

        parent::setUp();
    }
    
    public function testOrderItemCanBeCreated(): void
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
    
    public function testOrderItemCanBeCreatedByArray(): void
    {
        $order = OrderItem::create([
            'price'        => $this->price,
            'name'         => $this->name,
            'quantity'     => $this->quantity,
            'vatTag'       => $this->vatTag,
            'paymentMethod'=> $this->paymentMethod,
            'paymentObject'=> $this->paymentObject,
        ]);

        $this->assertEquals($order->getName(), $this->name);
        $this->assertEquals($order->getPrice(), $this->price);
        $this->assertEquals($order->getQuantity(), $this->quantity);
        $this->assertEquals($order->getVatTag(), $this->vatTag);
    }
    
    public function testOrderItemCanNotBeCreatedByArray(): void
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
    
    public function testOrderItemCanNotSetVatTag(): void
    {
        try {
            $order = new OrderItem();
            $order->setVatTag('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof VatTagNotAllowed);
        }
    }
    
    public function testOrderItemCanNotSetPaymentMethod(): void
    {
        try {
            $order = new OrderItem();
            $order->setPaymentMethod('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof PaymentMethodNotAllowed);
        }
    }
    
    public function testOrderItemCanNotSetPaymentObject(): void
    {
        try {
            $order = new OrderItem();
            $order->setPaymentObject('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof PaymentObjectNotAllowed);
        }
    }
}
