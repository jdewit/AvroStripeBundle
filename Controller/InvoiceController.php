<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Invoice controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class InvoiceController extends ContainerAware
{

    /**
     * List invoices
     */
    public function listAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        $invoices = \Stripe_Invoice::all(array('customer' => $user->getStripeCustomerId()))->data;

        $paginator = $this->container->get('knp_paginator')->paginate($invoices, $request->query->get('page', 1), 10);

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Invoice:list.html.twig', array(
            'paginator' => $paginator,
        ));
    }

    /**
     * Show an invoice
     */
    public function showAction($id)
    {
        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        $invoice = \Stripe_Invoice::retrieve($id);

        if (!$invoice) {
            throw new NotFoundHttpException('Invoice not found');
        }

        $user = $this->container->get('fos_user.user_manager')->findUserBy(array('stripeCustomerId' => $invoice->customer));

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $html = $this->container->get('templating')->render('AvroStripeBundle:Invoice:show.html.twig', array(
            'invoice' => $invoice,
            'user' => $user
        ));

        return new Response(
            $this->container->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$invoice->id.'.pdf"'
            )
        );
    }
}
