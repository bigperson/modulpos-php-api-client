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

use Bigperson\ModulposApiClient\Associate;

/**
 * Class AssociateTest.
 */
class AssociateTest extends TestCase
{
    public function testAssociateRetailPointAndOnlineShop(): array
    {
        $login = getenv('MODULPOS_LOGIN');
        $password = getenv('MODULPOS_PASSWORD');
        $retailPointUuid = getenv('MODULPOS_RETAIL_POINT_UUID');
        $associate = new Associate($login, $password, $retailPointUuid, true);
        $result = $associate->init();
        $this->assertTrue(is_array($result));
        $this->assertTrue(is_string($result['userName']));
        $this->assertTrue(is_string($result['password']));

        return $result;
    }
}
