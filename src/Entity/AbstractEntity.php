<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigperson\ModulposApiClient\Entity;

use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;

/**
 * Class AbstractEntity.
 */
abstract class AbstractEntity
{
    /**
     * @param array $params
     *
     * @throws \Bigperson\ModulposApiClient\Exceptions\MethodNotFound
     *
     * @return static
     */
    public static function create(array $params)
    {
        $item = new static();
        foreach ($params as $key => $param) {
            $methodName = 'set'.$key;
            if (method_exists($item, $methodName)) {
                $item->$methodName($param);
            } else {
                throw new MethodNotFound("Method is $methodName not found");
            }
        }

        return $item;
    }
}
