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

        $checkData = [
            'id'               => self::$documentId,
            'checkoutDateTime' => $dateTime->format(DATE_RFC3339),
            'docNum'           => rand(100000, 999999),
            'docType'          => 'SALE',
            'printReceipt'     => false,
            'email'            => 'test@example.com',
            'inventPositions'  =>
                [
                    0 => [
                            'barcode'  => rand(100000, 999999),
                            'discSum'  => 0,
                            'name'     => 'Подажа по свободной цене',
                            'price'    => rand(25, 5000),
                            'quantity' => 1,
                            'vatTag'   => 1107,
                        ],
                ],
            'moneyPositions'   =>
                [
                    0 => [
                            'paymentType' => 'CARD',
                            'sum'         => 23,
                        ],
                ],
            'responseURL'      => 'https://internet.shop.ru/order/982340931/checkout?completed=1',
        ];

        $result = $client->sendCheck($checkData);

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