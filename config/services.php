<?php

declare(strict_types=1);

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Ajgarlag\Bundle\OidcProviderBundle\OAuth2\IdTokenGrant;
use Ajgarlag\Bundle\OidcProviderBundle\Oidc\Response;
use Ajgarlag\Bundle\OidcProviderBundle\Repository\IdentityProvider;
use OpenIDConnectServer\ClaimExtractor;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('ajgarlag.oidc_provider.repository.identity_provider', IdentityProvider::class)
            ->args([service(EventDispatcherInterface::class)])
        ->alias(IdentityProviderInterface::class, 'ajgarlag.oidc_provider.repository.identity_provider')
        ->alias(IdentityProvider::class, 'ajgarlag.oidc_provider.repository.identity_provider')

        ->set('ajgarlag.oidc_provider.oidc.claim_extractor', ClaimExtractor::class)

        ->set('ajgarlag.oidc_provider.oidc.response', Response::class)
            ->args([
                service('ajgarlag.oidc_provider.repository.identity_provider'),
                service('ajgarlag.oidc_provider.oidc.claim_extractor'),
                service(EventDispatcherInterface::class),
                service(RequestStack::class),
            ])
        ->alias(Response::class, 'ajgarlag.oidc_provider.oidc.response')

        ->set('ajgarlag.oidc_provider.grant.id_token', IdTokenGrant::class)
            ->args([
                service('ajgarlag.oidc_provider.oidc.response'),
                null,
            ])
        ->alias(IdTokenGrant::class, 'ajgarlag.oidc_provider.grant.id_token')
    ;
};
