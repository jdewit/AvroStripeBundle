<?php

/**
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Avro\StripeBundle\Tests\Fixtures\Document;

use Avro\StripeBundle\Model\Plan as BasePlan;

class Plan extends BasePlan
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
