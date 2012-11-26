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

use Avro\StripeBundle\Form\Type\CouponFormType;

/**
 * Coupon controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CouponController extends ContainerAware
{
    /**
     * List all active coupons
     */
    public function listAction()
    {
        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        $coupons = \Stripe_Coupon::all()->data;

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Coupon:list.html.twig', array(
            'coupons' => $coupons
        ));
    }

    /**
     * Create new coupon
     */
    public function newAction(Request $request)
    {
        $form = $this->container->get('form.factory')->create(new CouponFormType());

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));
                $data = $form->getData();
                try {
                    \Stripe_Coupon::create(array(
                        'id' => $data['name'],
                        'percent_off' => $data['percentOff'],
                        'duration' => $data['duration'],
                        'duration_in_months' => $data['durationInMonths'],
                        'max_redemptions' => $data['maxRedemptions'],
                    ));

                    $this->container->get('session')->setFlash('success', 'coupon.created.flash');
                } catch(\Exception $e) {
                    $this->container->get('session')->setFlash('success', 'coupon.notCreated.flash');
                }

                return new RedirectResponse($this->container->get('router')->generate('avro_stripe_coupon_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('AvroStripeBundle:Coupon:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Delete a coupon
     */
    public function deleteAction($id)
    {
        \Stripe::setApiKey($this->container->getParameter('avro_stripe.secret_key'));

        try {
            $coupon = \Stripe_Coupon::retrieve($id);
            $coupon->delete();

            $this->container->get('session')->setFlash('success', 'coupon.deleted.flash');
        } catch(\Exception $e) {
            $this->container->get('session')->setFlash('error', 'coupon.notDeleted.flash');
        }

        return new RedirectResponse($this->container->get('router')->generate('avro_stripe_coupon_list'));
    }

}
