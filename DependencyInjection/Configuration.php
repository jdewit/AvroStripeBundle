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
                //->scalarNode('email_signature')->isRequired()->end()
                ->booleanNode('prorate')->defaultFalse()->end()
                ->booleanNode('hooks_enabled')->defaultFalse()->end()
                ->booleanNode('listener_enabled')->defaultTrue()->end()
                ->arrayNode('plan')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Application\StripeBundle\Document\Plan')->cannotBeEmpty()->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->defaultValue('Avro\StripeBundle\Form\Type\PlanFormType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('redirect_routes')
                    ->addDefaultsIfNotSet()
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
