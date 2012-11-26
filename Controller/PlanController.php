<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Plan controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PlanController extends ContainerAware
{
    /**
     * List all active plans
     */
    public function listAction($filter)
    {
        switch ($filter) {
            case 'Deleted':
                $plans = $this->container->get('avro_stripe.plan.manager')->findDeleted();
            break;
            default:
                $plans = $this->container->get('avro_stripe.plan.manager')->findActive();
            break;
        }

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Plan:list.html.twig', array(
            'plans' => $plans,
            'filter' => $filter
        ));
    }

    /**
     * Create new plan
     */
    public function newAction()
    {
        $form = $this->container->get('avro_stripe.plan.form');
        $formHandler = $this->container->get('avro_stripe.form.handler');

        try {
            if ($formHandler->process()) {
                $this->container->get('session')->setFlash('success', 'plan.created.flash');

                return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
            }
        } catch(\Stripe_Error $e) {
            $this->container->get('session')->setFlash('error', $e->getMessage());
        }

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Plan:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Edit a plan
     */
    public function editAction($id)
    {
        $plan = $this->container->get('avro_stripe.plan.manager')->find($id);

        $form = $this->container->get('avro_stripe.plan.form');
        $formHandler = $this->container->get('avro_stripe.form.handler');

        if ($formHandler->process($plan)) {
            $this->container->get('session')->setFlash('success', 'plan.updated.flash');

            return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
        }

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Plan:edit.html.twig', array(
            'plan' => $plan,
            'form' => $form->createView()
        ));
    }

    /**
     * Delete a plan
     */
    public function deleteAction($id)
    {
        $planManager = $this->container->get('avro_stripe.plan.manager');
        $stripePlanManager = $this->container->get('avro_stripe.stripe_plan.manager');

        $plan = $planManager->find($id);

        //delete on db
        $planManager->delete($plan);

        //delete on stripe
        $stripePlanManager->delete($plan);

        $this->container->get('session')->setFlash('success', 'plan.deleted.flash');

        return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
    }

    /**
     * Sync plans with Stripe
     */
    public function syncAction()
    {
        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        $planManager = $this->container->get('avro_stripe.plan.manager');

        $plans = $planManager->findAll();
        foreach($plans as $plan) {
            $planManager->remove($plan);
        }

        $plans = \Stripe_Plan::all()->data;
        foreach($plans as $plan) {
            $p = $planManager->create();
            $p->setId($plan->id);
            $p->setName($plan->name);
            $p->setInterval($plan->interval);
            $p->setAmount($plan->amount);
            $p->setCurrency($plan->currency);

            $planManager->update($p);
        }

        $this->container->get('session')->setFlash('success', 'plan.synced.flash');

        return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
    }

}
