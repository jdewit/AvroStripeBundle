<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Avro\StripeBundle\Model\PlanInterface;
use Avro\StripeBundle\Model\PlanManagerInterface;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PlanFormHandler
{
    protected $form;
    protected $request;
    protected $dispatcher;
    protected $planManager;
    protected $stripePlanManager;

    public function __construct(Form $form, Request $request, EventDispatcherInterface $dispatcher, PlanManagerInterface $planManager, $stripePlanManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->dispatcher = $dispatcher;
        $this->planManager = $planManager;
        $this->stripePlanManager = $stripePlanManager;
    }

    public function process(PlanInterface $plan = null)
    {
        if (!$plan) {
            $plan = $this->planManager->create();
            $isNew = true;
        } else {
            $isNew = false;
        }

        $this->form->setData($plan);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                //update on stripe
                if ($isNew) {
                    $this->stripePlanManager->create($plan);
                } else {
                    $this->stripePlanManager->update($plan);
                }

                //update on db
                $this->planManager->update($plan);

                return true;
            }
        }

        return false;
    }
}
