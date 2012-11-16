<?php

/**
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Avro\StripeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Avro\StripeBundle\Model\Plan;
use Avro\StripeBundle\Manager\PlanManager;

abstract class StripeTestCommon extends WebTestCase
{
    static $kernel;

    /**
     * PlanManager
     * @var Avro\StripeBundle\Manager\PlanManager
     */
    protected $planManager;

    public function getKernel(array $options = array())
    {
        if (!self::$kernel) {
            self::$kernel = $this->createKernel($options);
            self::$kernel->boot();
        }

        return self::$kernel;
    }

    public function getPlanManager()
    {
        if (!$this->planManager) {
            $this->planManager = $this->getMockForAbstractClass('Avro\StripeBundle\Model\PlanManager', array(
                'Avro\StripeBundle\Model\Plan'
            ));
        }

        return $this->planManager;
    }
}
