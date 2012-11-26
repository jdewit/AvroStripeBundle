<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('avro_stripe', 'array')
            ->children()
                ->scalarNode('db_driver')->defaultValue('mongodb')->cannotBeEmpty()->end()
                ->scalarNode('secret_key')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('publishable_key')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('client_id')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('email_signature')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('prorate')->defaultFalse()->cannotBeEmpty()->end()
                ->booleanNode('hooks_enabled')->defaultFalse()->cannotBeEmpty()->end()
                ->arrayNode('plan')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Application\StripeBundle\Document\Plan')->cannotBeEmpty()->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('avro_stripe_plan')->end()
                                ->scalarNode('name')->defaultValue('avro_stripe_plan_form')->end()
                                ->scalarNode('handler')->defaultValue('avro_stripe.plan.form.handler')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('redirect_routes')
                    ->children()
                        ->scalarNode('customer_new')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                        ->scalarNode('customer_update')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                        ->scalarNode('customer_disable')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                        ->scalarNode('subscription_update')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                        ->scalarNode('account_confirm')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                        ->scalarNode('account_disconnect')->defaultValue('avro_stripe_default_route')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
