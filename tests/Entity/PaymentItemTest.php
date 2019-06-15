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

namespace Tests\Order;

use Bigperson\ModulposApiClient\Entity\PaymentItem;
use Bigperson\ModulposApiClient\Exceptions\MethodNotFound;
use Bigperson\ModulposApiClient\Exceptions\TypeOperationsNotAllowed;
use Tests\TestCase;

/**
 * Class PaymentItemTest.
 */
class PaymentItemTest extends TestCase
{
    private $type;
    private $sum;
    
    public function setUp(): void 
    {
        $this->type = 'CARD';
        $this->sum = 100;

        parent::setUp();
    }
    
    public function testPaymentItemCanBeCreated(): void
    {
        $item = new PaymentItem();
        $item->setType($this->type);
        $item->setSum($this->sum);

        $this->assertEquals($item->getType(), $this->type);
        $this->assertEquals($item->getSum(), $this->sum);
    }
    
    public function testPaymentItemCanBeCreatedByArray(): void
    {
        $item = PaymentItem::create([
            'type'        => $this->type,
            'sum'         => $this->sum,
        ]);

        $this->assertEquals($item->getType(), $this->type);
        $this->assertEquals($item->getSum(), $this->sum);
    }
    
    public function testPaymentItemCanNotBeCreatedByArray(): void
    {
        try {
            $item = PaymentItem::create([
                'type'             => $this->type,
                'sum'              => $this->sum,
                'methodNotAllowed' => 'methodNotAllowed',
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof MethodNotFound);
        }
    }
    
    public function testPaymentItemCanNotSetType(): void
    {
        try {
            $item = new PaymentItem();
            $item->setType('NONE');
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof TypeOperationsNotAllowed);
        }
    }
}
