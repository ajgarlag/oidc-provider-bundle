<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OidcProviderBundle\OAuth2;

use League\OAuth2\Server\AuthorizationServer as LeagueAuthorizationServer;
use League\OAuth2\Server\Grant\GrantTypeInterface;

final class AuthorizationServer extends LeagueAuthorizationServer
{
    /**
     * @var non-empty-string[]
     */
    private $responseTypesSupported = [];

    public function enableGrantType(GrantTypeInterface $grantType, ?\DateInterval $accessTokenTTL = null): void
    {
        parent::enableGrantType($grantType, $accessTokenTTL);

        $this->addResponseTypeSupported($grantType);
    }

    private function addResponseTypeSupported(GrantTypeInterface $grantType): void
    {
        $responseTypes = match ($grantType->getIdentifier()) {
            'authorization_code' => ['code'],
            'client_credentials' => ['code'],
            'id_token' => IdTokenGrant::RESPONSE_TYPES,
            'implicit' => ['token'],
            'password' => ['code'],
            'refresh_token' => ['code'],
            'urn:ietf:params:oauth:grant-type:device_code' => ['code'],
            default => null,
        };

        if (\is_array($responseTypes)) {
            array_push($this->responseTypesSupported, ...$responseTypes);
        }

        $this->responseTypesSupported = array_unique($this->responseTypesSupported);
    }

    /**
     * @return non-empty-string[]
     */
    public function getResponseTypesSupported(): array
    {
        return $this->responseTypesSupported;
    }
}
