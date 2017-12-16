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

use Bigperson\ModulposApiClient\Config;

/**
 * Class ConfigTest.
 */
class ConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetBaseUrlTestMode()
    {
        $baseUrl = Config::getBaseUrl(true);

        $this->assertTrue($baseUrl === Config::BASE_TEST_URI);
    }

    /**
     * @return void
     */
    public function testGetBaseUrlWorkMode()
    {
        $baseUrl = Config::getBaseUrl();

        $this->assertTrue($baseUrl === Config::BASE_URI);
    }
}
