<?php
/**
 * This file is part of Modulpos package.
 *
 * @author Anton Kartsev <anton@alarm.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Tests\Order;

use Bigperson\ModulposApiClient\Entity\PaymentItem;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Bigperson\ModulposApiClient\Exceptions\TypeOperationsNotAllowed;
use Tests\TestCase;


/**
 * Class OrderItemTest
 *
 * @package Tests\Order
 */
class PaymentItemTest extends TestCase
{
    private $type;
    private $sum;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->type = 'CARD';
        $this->sum = 100;

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testPaymentItemCanBeCreated()
    {
        $item = new PaymentItem();
        $item->setType($this->type);
        $item->setSum($this->sum);

        $this->assertEquals($item->getType(), $this->type);
        $this->assertEquals($item->getSum(), $this->sum);
    }

    /**
     * @return void
     */
    public function testPaymentItemCanBeCreatedByArray()
    {
        $item = PaymentItem::create([
            'type'    => $this->type,
            'sum'         => $this->sum,
        ]);

        $this->assertEquals($item->getType(), $this->type);
        $this->assertEquals($item->getSum(), $this->sum);
    }

    /**
     * @return void
     */
    public function testPaymentItemCanNotBeCreatedByArray()
    {
        try {
            $item = PaymentItem::create([
                'type'    => $this->type,
                'sum'         => $this->sum,
                'methodNotAllowed' => 'methodNotAllowed',
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof MethodNotFound);
        }
    }

    /**
     * @return void
     */
    public function testPaymentItemCanNotSetType()
    {
        try {
            $item = new PaymentItem();
            $item->setType('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof TypeOperationsNotAllowed);
        }
    }
}