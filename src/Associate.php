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

use GuzzleHttp\ClientInterface;

/**
 * Class Associate
 * Класс
 */
class Associate
{
    /**
     * URL для запроса на ассоциацию.
     */
    const ASSOCIATE_URL = '/v1/associate/';

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $retailPointUuid;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var bool
     */
    private $testMode;

    /**
     * Associate constructor.
     *
     * Данные учетной записи Модуль.Касса используются только один раз - для
     * создания связки аккаунта и розничной точки.
     *
     * @param string $login
     * @param string $password
     * @param string $retailPointUuid
     * @param ClientInterface $client
     * @param bool $testMode
     */
    public function __construct(string $login, string $password, string $retailPointUuid, bool $testMode = false, ?ClientInterface $client = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->retailPointUuid = $retailPointUuid;
        $this->testMode = $testMode;
        $this->client = $client ?? new \GuzzleHttp\Client();
    }

    /**
     * Инициализация (связка) интернет-магазина с розничной точкой.
     *
     * После вызова метода связки, в ответе
     * выдается логин и пароль которые потом нужно использовать для всех обращений а
     * API. Данный метод надо вызывать единожды на на интеграцию - полученные учетные
     * данные нужно сохранить и вызывать все остальные методы с ними. Повторный вызов
     * нужно делать только при смене розничной точки, утери или компрометации
     * полученных учетных данных.
     *
     * @return array $response
     */
    public function init()
    {
        $authParams = ['auth' => [$this->login, $this->password]];
        $response = $this->client->request('POST', $this->getAssociateUrl(), $authParams);
        $result = $response->getBody();

        return json_decode($result->getContents(), true);
    }

    /**
     * @return string
     */
    private function getAssociateUrl()
    {
        return Config::getBaseUrl($this->testMode) . self::ASSOCIATE_URL . $this->retailPointUuid;
    }
}
