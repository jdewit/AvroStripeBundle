<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Customer controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CustomerController extends ContainerAware
{
    /**
     * Create a new Stripe customer
     *
     */
    public function newAction($planId)
    {
        $customerManager = $this->container->get('avro_stripe.customer.manager');

        if ('POST' == $this->container->get('request')->getMethod()) { // Process the token
            try {
                $customerManager->process($planId);

                $this->container->get('session')->getFlashBag()->set('success', 'Credit card added.');
            } catch (\Stripe_Error $e) {
                $this->container->get('session')->getFlashBag()->set('error', $e->getMessage());
            }

            $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.customer_new'));

            return new RedirectResponse($url);
        } else { // Show the credit card form
            return $this->container->get('templating')->renderResponse('AvroStripeBundle:Customer:new.html.twig', array(
                'planId' => $planId,
                'publishableKey' => $this->container->getParameter('avro_stripe.publishable_key')
            ));
        }
    }

    /**
     * Update a Stripe customer
     */
    public function updateAction()
    {
        $customerManager = $this->container->get('avro_stripe.customer.manager');

        if ('POST' == $this->container->get('request')->getMethod()) { // Process the token
            try {
                $customerManager->process();

                $this->container->get('session')->getFlashBag()->set('success', 'Credit card added.');
            } catch (\Stripe_Error $e) {
                $this->container->get('session')->getFlashBag()->set('error', $e->getMessage());
            }

            $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.customer_update'));

            return new RedirectResponse($url);
        } else { // Show the credit card form
            return $this->container->get('templating')->renderResponse('AvroStripeBundle:Customer:update.html.twig', array(
                'publishableKey' => $this->container->getParameter('avro_stripe.publishable_key')
            ));
        }
    }

    /**
     * Disable the user from paying with their credit card
     */
    public function disableAction()
    {
        $customerManager = $this->container->get('avro_stripe.customer.manager');

        try {
            $customerManager->disable();

            $this->container->get('session')->getFlashBag()->set('success', 'Credit card removed.');
        } catch (\Stripe_Error $e) {
            $this->container->get('session')->getFlashBag()->set('error', $e->getMessage());
        }

        $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.customer_disable'));

        return new RedirectResponse($url);
    }

    /**
     * Update a users Stripe account to subscribe to a plan
     */
    public function updateSubscriptionAction($planId)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $customerManager = $this->container->get('avro_stripe.customer.manager');

        if ('POST' == $this->container->get('request')->getMethod()) {
            try {
                $customerManager->process($planId);

                $this->container->get('session')->getFlashBag()->set('success', 'Plan updated.');
            } catch (\Stripe_Error $e) {
                $this->container->get('session')->getFlashBag()->set('error', $e->getMessage());
            }
        } else {
            if ($user->getIsStripeCustomerActive()) {
                $customerManager->update($planId);
            } else {
                return $this->container->get('templating')->renderResponse('AvroStripeBundle:Customer:updateSubscription.html.twig', array(
                    'planId' => $planId,
                    'publishableKey' => $this->container->getParameter('avro_stripe.publishable_key')
                ));
            }
        }

        $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.subscription_update'));

        return new RedirectResponse($url);
    }

}
