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

namespace Bigperson\ModulposApiClient;

use Bigperson\ModulposApiClient\Contracts\ClientInterface;
use Bigperson\ModulposApiClient\Contracts\ModulposCashierInterface;
use Bigperson\ModulposApiClient\Contracts\ModulposOrderInterface;

/**
 * Class Client.
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
     * @var \GuzzleHttp\Client|\GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Client constructor.
     *
     * В конструктор необходимо передать данные полученные
     * в результате ассоциализации интернет-магазина
     * с розничной точкой
     *
     * @param string                      $login
     * @param string                      $password
     * @param bool                        $testMode
     * @param \GuzzleHttp\ClientInterface $client
     *
     * @see \Bigperson\ModulposApiClient\Associate::init()
     */
    public function __construct(string $login, string $password, bool $testMode = false, \GuzzleHttp\ClientInterface $client = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->testMode = $testMode;
        $this->client = $client ?? new \GuzzleHttp\Client();
    }

    /**
     * Опрос готовности сервиса фискализации.
     *
     * @return array ['status', 'statusDateTime']
     */
    public function getStatusFiscalService()
    {
        $url = Config::getBaseUrl($this->testMode).self::STATUS_URI;

        return $this->send('GET', $url);
    }

    /**
     * Отправка данных чека на сервер фискализации (создание документа).
     *
     * @see https://support.modulkassa.ru/upload/medialibrary/abb/API%20%D0%B0%D0%B2%D1%82%D0%BE%D0%BC%D0%B0%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9%20%D1%84%D0%B8%D1%81%D0%BA%D0%B0%D0%BB%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8%20%D1%87%D0%B5%D0%BA%D0%BE%D0%B2%20%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82-%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD%D0%BE%D0%B2%20(ver.1.4).pdf
     *
     * @param ModulposOrderInterface        $order
     * @param null                          $responseUrl
     * @param bool                          $printReceipt
     * @param ModulposCashierInterface|null $cashier
     *
     * @return array|bool|float|int|string
     */
    public function sendCheck(ModulposOrderInterface $order, $responseUrl = null, $printReceipt = false, ModulposCashierInterface $cashier = null)
    {
        $url = Config::getBaseUrl($this->testMode).self::SEND_CHECK_DATA_URI;
        $checkData = CheckDataFactory::convertToArray($order, $responseUrl, $printReceipt, $cashier);

        return $this->send('POST', $url, $checkData);
    }

    /**
     * Проверка статуса документа.
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
     * @param string $method
     * @param string $url
     * @param array  $data
     *
     * @return array
     */
    private function send(string $method, string $url, array $data = []): array
    {
        $authParams = ['auth' => [$this->login, $this->password], 'json' => $data];
        $response = $this->client->request($method, $url, $authParams);

        return json_decode($response->getBody()->getContents(), true);
    }
}
