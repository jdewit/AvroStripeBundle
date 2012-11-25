<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Mailer implements MailerInterface
{
    protected $mailer;
    protected $context;
    protected $router;
    protected $templating;
    protected $parameters;

    public function __construct($mailer, $context, RouterInterface $router, EngineInterface $templating, array $parameters)
    {
        $this->mailer = $mailer;
        $this->context = $context;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
    }

    public function sendPlanUpdatedEmail(UserInterface $user)
    {
        // send message to user
        $rendered = $this->templating->render('AvroStripeBundle:Email/plan_updated.html.twig', array(
            'user' => $user,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    public function sendChargeSucceededEmail(UserInterface $user, $data)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email/charge_succeeded.html.twig', array(
            'user' => $user,
            'data' => $data,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    public function sendChargeFailedEmail(UserInterface $user, $data)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email/charge_failed.html.twig', array(
            'user' => $user,
            'data' => $data,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    public function sendInvoicePaymentSucceededEmail(UserInterface $user, $data)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email/invoice_payment_succeeded.html.twig', array(
            'user' => $user,
            'data' => $data,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    public function sendInvoicePaymentFailedEmail(UserInterface $user, $data)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email/invoice_payment_failed.html.twig', array(
            'user' => $user,
            'data' => $data,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }


    public function sendAccountConnectedEmail(UserInterface $user)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email:account_connected.html.twig', array(
            'user' => $user,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    public function sendAccountApplicationDeauthorizedEmail(UserInterface $user)
    {
        $rendered = $this->templating->render('AvroStripeBundle:Email/account_application_deauthorized.html.twig', array(
            'user' => $user,
            'from_name' => $this->parameters['from_name']
        ));
        $this->sendEmail($rendered, $this->parameters['from_email'], $user->getEmail());
    }

    protected function sendEmail($renderedTemplate, $fromEmail, $toEmail, $attachment=false, $html=false)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
        ;

        if ($html) {
            $message->setBody($body, 'text/html');
        } else {
            $message->setBody($body);
        }

        if ($attachment) {
            $message->attach(\Swift_Attachment::fromPath($attachment));
        }

        $this->mailer->send($message);
    }
}
