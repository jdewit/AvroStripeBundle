<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
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
    public function listAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        $invoices = \Stripe_Invoice::all(array('customer' => $user->getStripeCustomerId()))->data;

        //$paginator = $this->get('avro_paginator.paginator');
        //$paginator->setClass('ApplicationCoreBundle:Transaction');

        //$filters = array(
            //'invoices' => array(
                //'field' => 'debtor.id',
                //'value' => $user->getId(),
                //'label' => 'Invoices',
                //'icon' => 'sprite-money'
            //),
            //'payments' => array(
                //'field' => 'creditor.id',
                //'value' => $user->getId(),
                //'label' => 'Payments',
                //'icon' => 'sprite-expense'
            //)
        //);

        //$paginator->addFilters($filters);
        //$paginator->setDefaultFilter('invoices');

        //$transactions = $paginator->getResults();

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Invoice:list.html.twig', array(
            'invoices' => $invoices,
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

        $html = $this->container->get('templating')->render('AvroStripeBundle:Invoice:show.html.twig', array(
            'invoice' => $invoice,
        ));
        return new Response(
            $this->container->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="invoice_'.$invoice->id.'.pdf"'
            )
        );
    }
}
