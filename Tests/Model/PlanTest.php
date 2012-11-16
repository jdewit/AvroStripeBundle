<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Tests\Model;

use Avro\StripeBundle\Model\Plan;

class PlanTest extends \PHPUnit_Framework_TestCase
{
    public function testAmountInCents()
    {
        $plan = $this->getPlan();

        $this->assertEquals(0, $plan->getAmountInCents());
        $plan->setAmount(10);
        $this->assertEquals(1000, $plan->getAmountInCents());
    }

    public function testIsActiveDefaultIsTrue()
    {
        $plan = $this->getPlan();

        $this->assertEquals(true, $plan->getIsActive());
    }

    /**
     * @return Plan
     */
    protected function getPlan()
    {
        return $this->getMockForAbstractClass('Avro\StripeBundle\Model\Plan');
    }
}
