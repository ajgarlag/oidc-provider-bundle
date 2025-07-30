<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\Model;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\RegisteredClaims;

final class IdToken implements IdTokenInterface
{
    private function __construct(
        private readonly Plain $token,
    ) {
    }

    /**
     * @param non-empty-string $idToken
     */
    public static function fromString(string $idToken): self
    {
        $token = (new Parser(new JoseEncoder()))->parse($idToken);
        \assert($token instanceof Plain);

        return new self($token);
    }

    public static function fromJwtPlainToken(Plain $token): self
    {
        return new self($token);
    }

    public function getIssuer(): string
    {
        return $this->token->claims()->get(RegisteredClaims::ISSUER);
    }

    public function getSubject(): string
    {
        return $this->token->claims()->get(RegisteredClaims::SUBJECT);
    }

    /**
     * @return non-empty-string[]
     */
    public function getAudience(): array
    {
        return $this->token->claims()->get(RegisteredClaims::AUDIENCE);
    }

    public function getExpirationTime(): \DateTimeImmutable
    {
        return $this->token->claims()->get(RegisteredClaims::EXPIRATION_TIME);
    }

    public function getIssuedAtTime(): \DateTimeImmutable
    {
        return $this->token->claims()->get(RegisteredClaims::ISSUED_AT);
    }

    public function getAuthenticatedAtTime(): \DateTimeImmutable
    {
        return $this->token->claims()->get('auth_time');
    }

    public function getAuthorizedParty(): ?string
    {
        $authorizedParty = $this->token->claims()->get('azp');

        return \is_string($authorizedParty) && '' !== $authorizedParty ? $authorizedParty : null;
    }

    public function getClaim(string $name, mixed $default = null): mixed
    {
        return $this->token->claims()->get($name);
    }

    public function __toString(): string
    {
        return $this->token->toString();
    }
}
