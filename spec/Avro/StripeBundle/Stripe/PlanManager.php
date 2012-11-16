<?php

namespace spec\Avro\StripeBundle\Stripe;

use PHPSpec2\ObjectBehavior;

class PlanManager extends ObjectBehavior
{
    /**
     */
    function let()
    {
        $this->beConstructedWith(null);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Avro\StripeBundle\Stripe\PlanManager');
    }

    function it_should_retrieve_plan()
    {
        $this->retrieve(1)->shouldReturn('array');
    }
}
