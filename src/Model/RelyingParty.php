<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\Model;

use League\Bundle\OAuth2ServerBundle\Model\ClientInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;

class RelyingParty implements RelyingPartyInterface
{
    /** @var list<RedirectUri> */
    private array $postLogoutRedirectUris = [];

    /** @var non-empty-string */
    private ?string $frontChannelLogoutUri = null;

    /**
     * @param non-empty-string $identifier
     */
    public function __construct(
        private readonly string $identifier,
        private readonly ClientInterface $client,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @return list<RedirectUri>
     */
    public function getPostLogoutRedirectUris(): array
    {
        return $this->postLogoutRedirectUris;
    }

    public function setPostLogoutRedirectUris(RedirectUri ...$postLogoutRedirectUris): self
    {
        /** @var list<RedirectUri> $postLogoutRedirectUris */
        $this->postLogoutRedirectUris = $postLogoutRedirectUris;

        return $this;
    }

    public function getFrontChannelLogoutUri(): ?string
    {
        return $this->frontChannelLogoutUri;
    }

    public function setFrontChannelLogoutUri(?string $frontChannelLogoutUri): self
    {
        $this->frontChannelLogoutUri = $frontChannelLogoutUri;

        return $this;
    }
}
