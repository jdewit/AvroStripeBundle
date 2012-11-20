<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

use Avro\StripeBundle\Document\Account;

/**
 * Account controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class AccountController extends ContainerAware
{
    /**
     * Redirect to stripe connect page and prefill the form
     */
    public function connectAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $params = array_merge($user->getStripeParameters(), array(
            'response_type' => 'code',
            'client_id' => $this->container->getParameter('avro_stripe.client_id'),
            'scope' => 'read_write'
        ));

        $urlParams = http_build_query($params);

        $url = sprintf('https://connect.stripe.com/oauth/authorize?%s', $urlParams);

        return new RedirectResponse($url);
    }

    /**
     * Confirm stripe signup
     */
    public function confirmAction()
    {
        $request = $this->container->get('request');

        $code = $request->query->get('code');

        $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.account_confirm'));

        if ($code) {
            $context = $this->container->get('security.context');
            $userManager = $this->container->get('fos_user.user_manager');

            $auth_header = array(
              'Authorization: Bearer ' . $this->container->getParameter('avro_stripe.secret_key')
            );

            $token_request_body = array(
              'grant_type' => 'authorization_code',
              'client_id' => $this->container->getParameter('avro_stripe.client_id'),
              'code' => $code,
            );

            $req = curl_init('https://connect.stripe.com/oauth/token');
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true );
            curl_setopt($req, CURLOPT_HTTPHEADER, $auth_header);
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

            $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            curl_close($req);

            if (array_key_exists('error', $resp)) {
                $this->container->get('session')->getFlashBag()->set('error', 'Could not sync with Stripe. Please try again.');


                return new RedirectResponse($url);
            }

            $user = $context->getToken()->getUser();

            $user->setStripeAccountId($resp['stripe_user_id']);
            $user->setStripeAccessToken($resp['access_token']);
            $user->setStripePublishableKey($resp['stripe_publishable_key']);

            $userManager->updateUser($user);

            $this->get('avro_stripe.mailer')->sendAccountConnectedEmail($user);

            $this->container->get('session')->getFlashBag()->set('success', 'Stripe account synced!');
        } else {
            $this->container->get('session')->getFlashBag()->set('error', 'Unable to sync stripe account. Please try again.');
        }

        return new RedirectResponse($url);
    }

    /**
     * Stripe disconnect access
     */
    public function disconnectAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->container->get('security.context')->getToken()->getUser();

        $user->setStripeAccountId(null);
        $user->setStripeAccessToken(null);
        $user->setStripePublishableKey(null);

        $userManager->updateUser($user);

        $this->container->get('session')->getFlashBag()->set('success', 'Your Stripe account has been disconnected.');

        $url = $this->container->get('router')->generate($this->container->getParameter('avro_stripe.redirect_routes.account_disconnect'));

        return new RedirectResponse($url);
    }

}
