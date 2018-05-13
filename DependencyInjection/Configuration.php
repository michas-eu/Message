<?php

namespace Jasuwienas\MessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('message');


        $rootNode
            ->children()
                ->scalarNode('queue_object_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('smtp_mailer_user')->end()
               ->scalarNode('smtp_mailer_sender')->end()
                ->scalarNode('freshmail_api_host')->defaultValue('https://api.freshmail.com/')->end()
                ->scalarNode('freshmail_api_prefix')->defaultValue('rest/')->end()
                ->scalarNode('freshmail_api_api_key')->end()
                ->scalarNode('freshmail_api_secret_key')->end()
                ->scalarNode('sms_api_host')->defaultValue('https://api.smsapi.pl/sms.do')->end()
                ->scalarNode('sms_api_access_token')->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
