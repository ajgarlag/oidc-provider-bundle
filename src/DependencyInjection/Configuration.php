<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OidcProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ajgarlag_oidc_provider');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->append($this->createDiscoveryNode());

        return $treeBuilder;
    }

    private function createDiscoveryNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('discovery');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('authorization_endpoint_route')
                    ->info('Route name for the authorization endpoint')
                    ->defaultValue('oauth2_authorize')
                ->end()
                ->scalarNode('token_endpoint_route')
                    ->info('Route name for the token endpoint')
                    ->defaultValue('oauth2_token')
                ->end()
                ->scalarNode('jwks_endpoint_route')
                    ->info('Route name for the jwks endpoint')
                    ->defaultValue('oidc_jwks')
                ->end()
            ->end()
        ;

        return $node;
    }
}
