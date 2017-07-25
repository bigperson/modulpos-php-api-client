<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bigperson\ModulposApiClient\Requests;


use Bigperson\ModulposApiClient\Contracts\RequestInterface;
use Guzzle\Http\Client;

/**
 * Class Request
 *
 * @package Bigperson\ModulposApiClient\Requests
 */
class Request implements RequestInterface
{
    /**
     * Отправка запроса
     *
     * @param string $method
     * @param string $url
     * @param array  $authParams
     * @param array  $data
     *
     * @return array|bool|float|int|string
     */
    public function sendHttpRequest($method, $url, array $authParams, $data = [])
    {
        $client = new Client();

        switch ($method) {
            case 'POST':
                $request = $client->post($url);
                break;
            case 'GET':
                $request = $client->get($url);
                break;
            default:
                $request = $client->get($url);
        }

        $request->setAuth($authParams['login'], $authParams['password'])
                ->setHeader('Content-Type', 'application/json');

        if (!empty($data)) {
            $request->setBody(json_encode($data));
        }

        $response = $request->send();

        return $response->json();
    }
}