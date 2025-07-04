<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OidcProviderBundle\Controller;

use Ajgarlag\Bundle\OidcProviderBundle\OAuth2\AuthorizationServer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DiscoveryController
{
    public function __construct(
        private readonly AuthorizationServer $authorizationServer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $authorizationEndpointRoute,
        private readonly string $tokenEndpointRoute,
        private readonly string $jwksEndpointRoute,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(
            [
                'issuer' => $request->getSchemeAndHttpHost() . $request->getBasePath(),
                'authorization_endpoint' => $this->urlGenerator->generate($this->authorizationEndpointRoute, [], UrlGeneratorInterface::ABSOLUTE_URL),
                'token_endpoint' => $this->urlGenerator->generate($this->tokenEndpointRoute, [], UrlGeneratorInterface::ABSOLUTE_URL),
                'jwks_uri' => $this->urlGenerator->generate($this->jwksEndpointRoute, [], UrlGeneratorInterface::ABSOLUTE_URL),
                'response_types_supported' => $this->authorizationServer->getResponseTypesSupported(),
                'subject_types_supported' => ['public'],
                'id_token_signing_alg_values_supported' => ['RS256'],
            ],
            JsonResponse::HTTP_OK,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }
}
