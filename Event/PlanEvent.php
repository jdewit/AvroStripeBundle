<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Event;

use Avro\StripeBundle\Model\PlanInterface;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PlanEvent extends Event
{
    private $plan;

    /**
     * Constructs an event.
     *
     * @param $plan
     */
    public function __construct(PlanInterface $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Returns the plan for this event.
     *
     * @return $plan
     */
    public function getPlan()
    {
        return $this->plan;
    }
}
