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

use Bigperson\ModulposApiClient\Associate;
use Bigperson\ModulposApiClient\Client;
use Bigperson\ModulposApiClient\Entity\Order;
use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Entity\PaymentItem;


/**
 * Class ClientTest
 *
 * @package Tests
 */
class ClientTest extends TestCase
{
    /**
     * @var string
     */
    protected static $login;

    /**
     * @var string
     */
    protected static $password;

    /**
     * @var string
     */
    protected static $documentId;

    /**
     * Связывание торговой точки из данных переменных окружения
     */
    public static function setUpBeforeClass()
    {
        $login = getenv('MODULPOS_LOGIN');
        $password = getenv('MODULPOS_PASSWORD');
        $retailPointUuid = getenv('MODULPOS_RETAIL_POINT_UUID');

        $associate = new Associate($login, $password, $retailPointUuid, true);

        $result = $associate->init();

        self::$login = $result['userName'];

        self::$password = $result['password'];

        parent::setUpBeforeClass();
    }

    /**
     * Проверка статуса сервиса фискализации
     *
     * @return void
     */
    public function testGetStatusFiscalService()
    {
        $client = new Client(self::$login, self::$password, true);

        $result = $client->getStatusFiscalService();

        $this->assertTrue(is_array($result));

        $statuses = [
            'READY',
            'ASSOCIATED',
            'FAILED'
        ];

        $this->assertTrue(in_array($result['status'], $statuses));

        $this->assertTrue(is_string($result['dateTime']));
    }

    /**
     * Проверка отправки данных чека на сервер фискализации
     *
     * @return void
     */
    public function testSendCheck()
    {
        $this->assertEmpty(self::$documentId);

        $client = new Client(self::$login, self::$password, true);


        date_default_timezone_set('Europe/Moscow');
        $dateTime =  new \DateTime('NOW');

        self::$documentId = uniqid();

        $order = Order::create([
            'documentUuid'       => self::$documentId,
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

        $responseUrl =  'https://internet.shop.ru/order/982340931/checkout?completed=1';

        $printReceipt = true;

        $result = $client->sendCheck($order, $responseUrl, $printReceipt);

        $this->assertTrue(is_array($result));

        $statuses = [
            'QUEUED',
            'PENDING',
            'PRINTED',
            'COMPLETED',
            'FAILED'
        ];

        $this->assertTrue(in_array($result['status'], $statuses));
    }

    /**
     * Проверка статуса документа (чека)
     *
     * @return void
     */
    public function testGetDocumentStatus()
    {
        $this->assertNotEmpty(self::$documentId);

        $client = new Client(self::$login, self::$password, true);

        $result = $client->getStatusDocumentById(self::$documentId);

        $this->assertTrue(is_array($result));

        $statuses = [
            'QUEUED',
            'PENDING',
            'PRINTED',
            'COMPLETED',
            'FAILED'
        ];

        $this->assertTrue(in_array($result['status'], $statuses));
    }


}