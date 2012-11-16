<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAware;
use Avro\StripeBundle\Event\HookEvent;

/**
 * Hook controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class HookController extends ContainerAware
{
    /**
     * Receive event hooks and dispatch our own event
     */
    public function indexAction()
    {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $event = $content['type'];
        $data = $content['data']['object'];

        $dispatcher = $this->container->get('dispatcher');
        $dispatcher->dispatch(sprintf('avro_stripe.hook.%s', $event), new HookEvent($data));

        return new Response('ok', 200, array('Content-Type'=>'text/html'));
    }
}
