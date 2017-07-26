<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bigperson\ModulposApiClient;

use Bigperson\ModulposApiClient\Contracts\ClientInterface;
use Bigperson\ModulposApiClient\Contracts\ModulposOrderInterface;
use Bigperson\ModulposApiClient\Contracts\ModulposOrderItemInterface;
use Bigperson\ModulposApiClient\Requests\Request;


/**
 * Class Client
 *
 * @package Bigperson\ModulposApiClient
 */
class Client implements ClientInterface
{
    const STATUS_URI = '/v1/status';
    const SEND_CHECK_DATA_URI = '/v1/doc';
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $testMode;

    /**
     * Client constructor.
     *
     * В конструктор необходимо передать данные полученные
     * в результате ассоциализации интернет-магазина
     * с розничной точкой
     *
     * @see \Bigperson\ModulposApiClient\Associate::init()
     *
     * @param string $login
     * @param string $password
     * @param bool   $testMode
     */
    public function __construct($login, $password, $testMode = false)
    {
        $this->login = $login;
        $this->password = $password;
        $this->testMode = $testMode;
    }


    /**
     * Опрос готовности сервиса фискализации
     *
     * @return array ['status', 'statusDateTime']
     */
    public function getStatusFiscalService()
    {
        $url = Config::getBaseUrl($this->testMode).self::STATUS_URI;

        return $this->send('GET', $url);
    }

    /**
     * Отправка данных чека на сервер фискализации (создание документа)
     *
     * @see http://modulkassa.ru/upload/medialibrary/abb/api-avtomaticheskoy-fiskalizatsii-chekov-internet_magazinov-_ver.1.2_.pdf
     *
     * @param ModulposOrderInterface $order
     * @param null                       $responseUrl
     * @param bool                       $printReceipt
     *
     * @return array|bool|float|int|string
     */
    public function sendCheck(ModulposOrderInterface $order, $responseUrl = null, $printReceipt = false)
    {
        $url = Config::getBaseUrl($this->testMode).self::SEND_CHECK_DATA_URI;

        $checkData = CheckDataFactory::convertToArray($order,  $responseUrl, $printReceipt);

        return $this->send('POST', $url, $checkData);
    }

    /**
     * Проверка статуса документа
     *
     * @param $documentId
     *
     * @return array
     */
    public function getStatusDocumentById($documentId)
    {
        $url = Config::getBaseUrl($this->testMode).self::SEND_CHECK_DATA_URI.'/'.$documentId.'/status';

        return $this->send('GET', $url);
    }

    /**
     * @param $method
     * @param $url
     * @param $data
     *
     * @return array|bool|float|int|string
     */
    private function send($method, $url, $data = [])
    {
        $request = new Request();

        $authParams = [
            'login' => $this->login,
            'password' => $this->password
        ];

        $response = $request->sendHttpRequest($method, $url, $authParams, $data);

        return $response;
    }
}