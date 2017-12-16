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

/**
 * Class Config.
 */
class Config
{
    /**
     * Базовый URL сервиса.
     */
    const BASE_URI = 'https://service.modulpos.ru/api/fn';

    /**
     * Базовый URL сервиса для тестирования.
     */
    const BASE_TEST_URI = 'https://demo-fn.avanpos.com/fn';

    /**
     * Возвращает базовый URL.
     *
     * @param bool $testMode
     *
     * @return string
     */
    public static function getBaseUrl($testMode = false)
    {
        if ($testMode) {
            return self::BASE_TEST_URI;
        }

        return self::BASE_URI;
    }
}
