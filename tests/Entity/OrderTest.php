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


use Bigperson\ModulposApiClient\Entity\Order;
use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Entity\PaymentItem;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Bigperson\ModulposApiClient\Exceptions\TypeOperationsNotAllowed;
use Tests\TestCase;

/**
 * Class ConfigTest
 *
 * @package Bigperson\ModulposApiClient\Tests
 */
class OrderTest extends TestCase
{

    private $uuid;
    private $orderId;
    private $customerContact;
    private $typeOperation;
    /**
     * @var \DateTime
     */
    private $checkoutDateTime;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->uuid = uniqid();
        $this->orderId = rand(1000, 9999);
        $this->customerContact = 'test@test.ru';
        $this->typeOperation = 'SALE';
        date_default_timezone_set('Europe/Moscow');
        $this->checkoutDateTime = new \DateTime('NOW');

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testOrderCanBeCreated()
    {
        $order = new Order();
        $order->setDocumentUuid($this->uuid);
        $order->setOrderId($this->orderId);
        $order->setCustomerContact($this->customerContact);
        $order->setTypeOperation($this->typeOperation);
        $order->setCheckoutDateTime($this->checkoutDateTime->format(DATE_RFC3339));

        $this->assertEquals($order->getDocumentUuid(), $this->uuid);
        $this->assertEquals($order->getOrderId(), $this->orderId);
        $this->assertEquals($order->getCustomerContact(), $this->customerContact);
        $this->assertEquals($order->getTypeOperation(), $this->typeOperation);
        $this->assertEquals($order->getCheckoutDateTime(), $this->checkoutDateTime->format(DATE_RFC3339));
    }

    /**
     * @return void
     */
    public function testOrderCanBeCreatedByArray()
    {
        $order = Order::create([
            'documentUuid'    => $this->uuid,
            'orderId'         => $this->orderId,
            'customerContact' => $this->customerContact,
            'typeOperation'   => $this->typeOperation,
            'checkoutDateTime' => $this->checkoutDateTime->format(DATE_RFC3339)
        ]);

        $this->assertEquals($order->getDocumentUuid(), $this->uuid);
        $this->assertEquals($order->getOrderId(), $this->orderId);
        $this->assertEquals($order->getCustomerContact(), $this->customerContact);
        $this->assertEquals($order->getTypeOperation(), $this->typeOperation);
        $this->assertEquals($order->getCheckoutDateTime(), $this->checkoutDateTime->format(DATE_RFC3339));
    }

    /**
     * @return void
     */
    public function testOrderCanNotBeCreatedByArray()
    {
        try {
            $order = Order::create([
                'documentUuid'     => $this->uuid,
                'orderId'          => $this->orderId,
                'customerContact'  => $this->customerContact,
                'typeOperation'    => $this->typeOperation,
                'methodNotAllowed' => 'methodNotAllowed',
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof MethodNotFound);
        }
    }

    /**
     * @return void
     */
    public function testOrderCanNotSetTypeOperator()
    {
        try {
            $order = new Order();
            $order->setTypeOperation('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof TypeOperationsNotAllowed);
        }
    }

    /**
     * @return void
     */
    public function testOrderItemsCanAdd()
    {
        $order = new Order();
        $orderItem1 = new OrderItem();
        $orderItem2 = new OrderItem();

        $order->addItem($orderItem1);
        $order->addItem($orderItem2);

        $this->assertEquals($order->getItems(), [$orderItem1, $orderItem2]);
    }

    /**
     * @return void
     */
    public function testPaymentItemsCanAdd()
    {
        $order = new Order();
        $paymentItem1 = new PaymentItem();
        $paymentItem2 = new PaymentItem();

        $order->addPaymentItem($paymentItem1);
        $order->addPaymentItem($paymentItem2);


        $this->assertEquals($order->getPaymentItems(), [$paymentItem1, $paymentItem2]);
    }
}