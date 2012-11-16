<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Tests;

use Avro\StripeBundle\Model\Plan;

class TestPlan extends Plan
{
    public function setId($id)
    {
        $this->id = $id;
    }
}
