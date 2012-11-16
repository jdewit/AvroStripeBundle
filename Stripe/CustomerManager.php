<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\StripeBundle\Manager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomerManager
{
    protected $request;
    protected $context;
    protected $userManager;
    protected $prorate;

    public function __construct(Request $request, $context, $userManager, $secretKey, $prorate)
    {
        $this->request = $request;
        $this->context = $context;
        $this->userManager = $userManager;
        $this->prorate = $prorate;

        \Stripe::setApiKey($secretKey);
    }

    /**
     * Create a Stripe customer
     */
    public function create($planId = false)
    {
        $user = $this->context->getToken()->getUser();

        if ($planId) {
            $customer = \Stripe_Customer::create(array(
              "card" => $this->getToken(),
              "email" => $user->getEmail(),
              "plan" => $planId,
              "coupon" => $user->getCoupon()
            ));
        } else {
             $customer = \Stripe_Customer::create(array(
              "card" => $this->getToken(),
              "email" => $user->getEmail(),
            ));
        }

        $card = $customer->active_card;

        $user->setStripeCustomerId($customer->id);
        $user->setIsStripeCustomerActive(true);
        $user->setStripeCardReference(sprintf('%s - %s', $card->type, $card->last4));

        $this->userManager->updateUser($user);

        return true;
    }

    /*
     * Update a Stripe Customer
     */
    public function update($planId)
    {
        $user = $this->context->getToken()->getUser();

        $customer = \Stripe_Customer::retrieve($user->getStripeCustomerId());

        $token = $this->getToken();

        if ($token) {
            $customer->card = $token;
            $customer->save();

            $card = $customer->active_card;

            $user->setStripeCardReference(sprintf('%s - %s', $card->type, $card->last4));
            $user->setIsStripeCustomerActive(true);

            $this->userManager->updateUser($user);
        }

        if ($planId) {
            $customer->updateSubscription(array(
                "prorate" => $this->prorate,
                "plan" => $planId
            ));

            $user->setStripePlan($planId);
            $this->userManager->updateUser($user);
        }

        return true;
    }

    /**
     * Disable Stripe customer
     */
    public function disable()
    {
        $user = $this->context->getToken()->getUser();

        $stripeCustomerId = $user->getStripeCustomerId();

        $customer = \Stripe_Customer::retrieve($stripeCustomerId);
        $customer->active_card = null;
        $customer->save();

        $user->setIsStripeCustomerActive(false);

        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Cancel Stripe customers subscription
     */
    public function cancelSubscription()
    {
        $user = $this->context->getToken()->getUser();
        $stripeCustomerId = $user->getStripeCustomerId();

        $customer = \Stripe_Customer::retrieve($stripeCustomerId);

        $customer->cancelSubscription();

        $user->setIsStripeCustomerActive(false);

        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Get credit card token
     */
    public function getToken()
    {
        return $this->request->request->get('stripeToken');
    }
}
