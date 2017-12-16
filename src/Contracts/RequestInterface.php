<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigperson\ModulposApiClient\Contracts;

/**
 * Interface RequestInterface.
 */
interface RequestInterface
{
    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     *
     * @return mixed
     */
    public function sendHttpRequest($method, $url, array $params);
}
