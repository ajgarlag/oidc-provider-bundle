<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\Model;

interface IdTokenInterface extends \Stringable
{
    /**
     * @return non-empty-string
     */
    public function getIssuer(): string;

    /**
     * @return non-empty-string
     */
    public function getSubject(): string;

    /**
     * @return non-empty-string[]
     */
    public function getAudience(): array;

    public function getExpirationTime(): \DateTimeImmutable;

    public function getIssuedAtTime(): \DateTimeImmutable;

    public function getAuthenticatedAtTime(): ?\DateTimeImmutable;

    /**
     * @return non-empty-string|null
     */
    public function getAuthorizedParty(): ?string;

    /**
     * @param non-empty-string $name
     */
    public function getClaim(string $name, mixed $default = null): mixed;

    /**
     * @return non-empty-string
     */
    public function __toString(): string;
}
