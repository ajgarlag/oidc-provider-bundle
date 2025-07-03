<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OidcProviderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AuthorizationServerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $authorizationServerDefinition = $container->getDefinition('league.oauth2_server.authorization_server');
        $authorizationServerDefinition->setArgument(5, new Reference('ajgarlag.oidc_provider.oidc.response'));

        $authorizationServerDefinition->addMethodCall('enableGrantType', [
            new Reference('ajgarlag.oidc_provider.grant.id_token'),
        ]);
    }
}
