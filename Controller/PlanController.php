<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
            case 'Disabled':
                $plans = $this->container->get('avro_stripe.plan.manager')->findBy(array('isActive' => false));
            break;
            default:
                $plans = $this->container->get('avro_stripe.plan.manager')->findBy(array('isActive' => true));
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
    public function newAction(Request $request)
    {
        $form = $this->container->get('avro_stripe.plan.form');
        $planManager = $this->container->get('avro_stripe.plan.manager');

        $plan = $planManager->create();

        $form->setData($plan);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                try {
                    $plan = $planManager->persist($plan);
                    $request->getSession()->setFlash('success', 'plan.created.flash');
                } catch(\Stripe_Error $e) {
                    $request->getSession()->setFlash('error', $e->getMessage());
                }

                return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Plan:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Disable a plan
     */
    public function disableAction($id)
    {
        $plan = $this->container->get('avro_stripe.plan.manager')->find($id);

        if ($plan instanceof Plan) {
            $plan->setIsActive(false);

            $plan = $planManager->update($plan);

            $this->container->get('session')->setFlash('success', 'plan.updated.flash');
        }

        return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
    }

    /**
     * Enable a plan
     */
    public function enableAction($id)
    {
        $plan = $this->container->get('avro_stripe.plan.manager')->find($id);

        if ($plan instanceof Plan) {
            $plan->setIsActive(true);

            $plan = $planManager->update($plan);

            $this->container->get('session')->setFlash('success', 'plan.updated.flash');
        }

        return new RedirectResponse($this->container->get('router')->generate('avro_stripe_plan_list'));
    }

    /**
     * Delete a plan
     */
    public function deleteAction($id)
    {
        $planManager = $this->container->get('avro_stripe.plan.manager');

        $plan = $planManager->find($id);

        $planManager->delete($plan);

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
