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

namespace Tests;

use Bigperson\ModulposApiClient\Config;

/**
 * Class ConfigTest.
 */
class ConfigTest extends TestCase
{
    public function testGetBaseUrlTestMode(): void
    {
        $baseUrl = Config::getBaseUrl(true);

        $this->assertTrue($baseUrl === Config::BASE_TEST_URI);
    }

    public function testGetBaseUrlWorkMode(): void
    {
        $baseUrl = Config::getBaseUrl();

        $this->assertTrue($baseUrl === Config::BASE_URI);
    }
}
