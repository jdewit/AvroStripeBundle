<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class AvroStripeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) {

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        switch($config['db_driver']) {
            case 'mongodb':
                $loader->load('mongodb.yml');
            break;
            case 'orm':
                //TODO
            break;
        }

		$loader->load('plan.yml');

		if ($config['hooks_enabled']) {
			$loader->load('mailer.yml');
			$loader->load('hook.yml');
		}

//   			\Stripe::setApiKey($container->getParameter('secret_key');

        $container->setParameter('avro_stripe.prorate', $config['prorate']);
        //$container->setParameter('avro_stripe.email_signature', $config['email_signature']);

        $container->setParameter('avro_stripe.redirect_routes.customer_new', $config['redirect_routes']['customer_new']);
        $container->setParameter('avro_stripe.redirect_routes.customer_update', $config['redirect_routes']['customer_update']);
        $container->setParameter('avro_stripe.redirect_routes.customer_disable', $config['redirect_routes']['customer_disable']);
        $container->setParameter('avro_stripe.redirect_routes.subscription_update', $config['redirect_routes']['subscription_update']);
        $container->setParameter('avro_stripe.redirect_routes.account_confirm', $config['redirect_routes']['account_confirm']);
        $container->setParameter('avro_stripe.redirect_routes.account_disconnect', $config['redirect_routes']['account_disconnect']);

        $container->setParameter('avro_stripe.plan.class', $config['plan']['class']);
        $container->setParameter('avro_stripe.plan.form.type', $config['plan']['form']['type']);
        $container->setParameter('avro_stripe.plan.form.name', $config['plan']['form']['name']);
        $container->setParameter('avro_stripe.plan.form.handler', $config['plan']['form']['handler']);
    }
}

