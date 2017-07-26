<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Tests;

use Bigperson\ModulposApiClient\CheckDataFactory;
use Bigperson\ModulposApiClient\Entity\Order;
use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Entity\PaymentItem;


/**
 * Class CheckDataFactoryTest
 *
 * @package Tests
 */
class CheckDataFactoryTest extends TestCase
{
     /**
     * @return void
     */
    public function testConvertOrderToArray()
    {
        date_default_timezone_set('Europe/Moscow');
        $dateTime =  new \DateTime('NOW');

        $order = Order::create([
            'documentUuid'     => uniqid(),
            'checkoutDateTime' => $dateTime->format(DATE_RFC3339),
            'orderId'          => rand(100000, 999999),
            'typeOperation'    => 'SALE',
            'customerContact'  => 'test@example.com',
        ]);

        $orderItem1 = OrderItem::create([
           'price' => 100,
            'quantity' => 1,
            'vatTag' => OrderItem::VAT_NO,
            'name' => 'Test Product1'
        ]);

        $orderItem2 = OrderItem::create([
            'price' => 200,
            'quantity' => 1,
            'vatTag' => OrderItem::VAT_NO,
            'name' => 'Test Product2'
        ]);

        $paymentItem = PaymentItem::create([
            'type' => 'CARD',
            'sum' => 300
        ]);

        $order->addItem($orderItem1);
        $order->addItem($orderItem2);
        $order->addPaymentItem($paymentItem);

        $checkData = CheckDataFactory::convertToArray($order);

        $this->assertTrue(is_array($checkData));

        $this->assertNotEmpty($checkData);
    }

    public function testValidate()
    {
        $order = new Order();

        try{
            $checkData = CheckDataFactory::convertToArray($order);
        } catch (\Exception $exception){
            $this->assertTrue($exception instanceof \Exception);
        }
    }


}