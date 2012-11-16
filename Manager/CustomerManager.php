<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\StripeBundle\Manager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

use FOS\UserBundle\Model\UserManagerInterface;
use Avro\StripeBundle\Model\PlanManagerInterface;

/**
 * Processes a Stripe token and adds the Stripe Customer to the User
 * as well as subscribe the user to a plan
 */
class CustomerManager
{
    protected $request;
    protected $user;
    protected $userManager;
    protected $planManager;
    protected $prorate;

    public function __construct(Request $request, SecurityContextInterface $context, UserManagerInterface $userManager, PlanManagerInterface $planManager, $secretKey, $prorate)
    {
        $this->request = $request;
        $this->user = $context->getToken()->getUser();
        $this->userManager = $userManager;
        $this->planManager = $planManager;
        $this->prorate = $prorate;

        \Stripe::setApiKey($secretKey);
    }

    /**
     * Process a Stripe token
     */
    public function process($planId = false)
    {
        if ($this->user->getStripeCustomerId()) {
            $this->update($planId);
        } else {
            $this->create($planId);
        }
    }

    /**
     * Create a stripe customer
     *
     * @param string $planId
     */
    public function create($planId = false)
    {
        if ($planId) {
            $customer = \Stripe_Customer::create(array(
              "card" => $this->getToken(),
              "email" => $this->user->getEmail(),
              "plan" => $planId,
              "coupon" => $this->user->getCoupon()
            ));

            $plan = $this->planManager->find($planId);

            $this->user->setPlan($plan);
        } else {
             $customer = \Stripe_Customer::create(array(
              "card" => $this->getToken(),
              "email" => $this->user->getEmail(),
            ));
        }

        $this->user->setStripeCustomerId($customer->id);

        $card = $customer->active_card;
        if ($card) {
            $this->user->setIsStripeCustomerActive(true);
            $this->user->setStripeCardReference(sprintf('%s - %s', $card->type, $card->last4));
        }

        $this->userManager->updateUser($this->user);

        return true;
    }

    /*
     * Update a Stripe Customer
     */
    public function update($planId)
    {
        $customer = $this->retrieve($this->user->getStripeCustomerId());

        $token = $this->getToken();

        if ($token) {
            $customer->card = $token;
            $customer->save();

            $card = $customer->active_card;

            if ($card) {
                $this->user->setStripeCardReference(sprintf('%s - %s', $card->type, $card->last4));
                $this->user->setIsStripeCustomerActive(true);
            }
        }

        if ($planId) {
            $customer->updateSubscription(array(
                "prorate" => $this->prorate,
                "plan" => $planId
            ));

            $plan = $this->planManager->find($planId);

            $this->user->setPlan($plan);
        }

        $this->userManager->updateUser($this->user);

        return true;
    }

    /**
     * Disable Stripe customer
     */
    public function disable()
    {
        $stripeCustomerId = $this->user->getStripeCustomerId();

        $customer = $this->retrieve($stripeCustomerId);

        $customer->active_card = null;
        $customer->save();

        $this->user->setIsStripeCustomerActive(false);

        $this->userManager->updateUser($this->user);

        return true;
    }

    /**
     * Cancel Stripe customers subscription
     */
    public function cancelSubscription()
    {
        $stripeCustomerId = $this->user->getStripeCustomerId();

        $customer = $this->retrieve($stripeCustomerId);

        $customer->cancelSubscription();

        $this->user->setIsStripeCustomerActive(false);
        $this->user->setPlan(null);

        $this->userManager->updateUser($this->user);

        return true;
    }

    /**
     * Retrieve a customer from Stripe
     */
    public function retrieve($id) {
        try {
            return \Stripe_Customer::retrieve($id);
        } catch (\Stripe_Error $e) {
            throw new \Stripe_Error('Customer not found');
        }
    }

    /**
     * Get credit card token
     */
    public function getToken()
    {
        return $this->request->request->get('stripeToken');
    }
}
