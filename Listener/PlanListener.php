<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Listener;

use Avro\StripeBundle\Event\PlanEvent;
use Avro\StripeBundle\Api\PlanApiHelper;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PlanListener {

    public function __construct(PlanApiHelper $planApiHelper)
    {
        $this->planApiHelper = $planApiHelper;
    }

    public function createPlanOnStripe(PlanEvent $event)
    {
        $plan = $event->getPlan();

        $this->planApiHelper->create($plan);
    }

    public function updatePlanOnStripe(PlanEvent $event)
    {
        $plan = $event->getPlan();

        $this->planApiHelper->update($plan);
    }

    public function deletePlanOnStripe(PlanEvent $event)
    {
        $plan = $event->getPlan();

        $this->planApiHelper->delete($plan);
    }
}
