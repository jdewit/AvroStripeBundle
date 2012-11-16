<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Listener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

use Avro\StripeBundle\Event\HookEvent;
use Avro\StripeBundle\Mailer\MailerInterface;

use FOS\UserBundle\Model\UserManagerInterface;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class HookListener {
    protected $mailer;
    protected $userManager;

    public function __construct(MailerInterface $mailer, UserManagerInterface $userManager) {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    public function chargeSucceeded(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        $this->mailer->sendChargeSucceededEmail($user, $data);
    }

    public function chargeFailed(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        $this->mailer->sendChargeFailedEmail($user, $data);
    }

    public function invoiceCreated(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        // create an invoice in our db
        $invoice = $this->invoiceManager->create();
        $invoice->setAmount();
        $this->invoiceManager->update($invoice);
    }

    public function invoicePaymentSucceeded(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        $this->mailer->sendInvoicePaymentSucceededEmail($user, $data);
    }

    public function invoicePaymentFailed(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        $this->mailer->sendInvoicePaymentFailedEmail($user, $data);
    }

    public function accountApplicationDeauthorized(HookEvent $event) {
        $data = $event->getData();
        $user = $this->getUser($data);

        $user->setStripeAccount(null);

        $this->userManager->update($user);

        $this->mailer->sendAccountApplicationDeauthorizedEmail($user);
    }

    protected function getUser($data)
    {
        return $this->userManager->findUserBy(array('stripeCustomer.id' => $data['customer']));
    }
}

