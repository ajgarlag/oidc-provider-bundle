<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OidcProviderBundle\Tests\Acceptance;

use Ajgarlag\Bundle\OidcProviderBundle\Model\IdToken;
use Ajgarlag\Bundle\OidcProviderBundle\Tests\Fixtures\FixtureFactory;
use League\Bundle\OAuth2ServerBundle\Event\AuthorizationRequestResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use League\Bundle\OAuth2ServerBundle\Tests\Acceptance\AuthorizationEndpointTest as LeagueAuthorizationEndpointTest;
use League\Bundle\OAuth2ServerBundle\Tests\TestHelper;

final class AuthorizationEndpointTest extends LeagueAuthorizationEndpointTest
{
    use AcceptanceTestTrait;

    public function testSuccessfulCodeRequest(): void
    {
        $this->assertTrue(true);
        $this->client
            ->getContainer()
            ->get('event_dispatcher')
            ->addListener(OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE, static function (AuthorizationRequestResolveEvent $event): void {
                $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
            });

        $this->loginUser();

        $this->client->request(
            'GET',
            '/authorize',
            [
                'client_id' => FixtureFactory::FIXTURE_CLIENT_OIDC,
                'response_type' => 'code',
                'state' => 'foobar',
                'scope' => 'openid',
                'nonce' => 'n0nc3',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());
        $redirectUri = $response->headers->get('Location');

        $this->assertStringStartsWith(FixtureFactory::FIXTURE_CLIENT_OIDC_REDIRECT_URI, $redirectUri);
        $query = [];
        parse_str(parse_url((string) $redirectUri, \PHP_URL_QUERY), $query);
        $this->assertArrayHasKey('code', $query);
        $payload = json_decode(TestHelper::decryptPayload($query['code']), true);
        $this->assertArrayHasKey('nonce', $payload);
        $this->assertSame($payload['nonce'], 'n0nc3');
        $this->assertArrayHasKey('state', $query);
        $this->assertEquals('foobar', $query['state']);
    }

    /**
     * @group time-sensitive
     */
    public function testSuccessfulImplicitRequest(): void
    {
        $this->client
            ->getContainer()
            ->get('event_dispatcher')
            ->addListener(OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE, static function (AuthorizationRequestResolveEvent $event): void {
                $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
            });

        $this->loginUser();

        $this->client->request(
            'GET',
            '/authorize',
            [
                'client_id' => FixtureFactory::FIXTURE_CLIENT_OIDC,
                'response_type' => 'id_token token',
                'state' => 'foobar',
                'scope' => 'openid',
                'redirect_uri' => FixtureFactory::FIXTURE_CLIENT_OIDC_REDIRECT_URI,
                'nonce' => 'n0nc3',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());
        $redirectUri = $response->headers->get('Location');

        $this->assertStringStartsWith(FixtureFactory::FIXTURE_CLIENT_OIDC_REDIRECT_URI, $redirectUri);
        $fragment = [];
        parse_str(parse_url($redirectUri, \PHP_URL_FRAGMENT), $fragment);
        $this->assertArrayHasKey('id_token', $fragment);
        $idToken = IdToken::fromString($fragment['id_token']);
        $this->assertSame('user', $idToken->getSubject());
        $this->assertSame(['client_oidc'], $idToken->getAudience());
        $this->assertSame('n0nc3', $idToken->getClaim('nonce'));
        $this->assertArrayHasKey('state', $fragment);
        $this->assertEquals('foobar', $fragment['state']);
        $this->assertArrayHasKey('access_token', $fragment);
        $this->assertArrayHasKey('token_type', $fragment);
        $this->assertEquals('Bearer', $fragment['token_type']);
        $this->assertArrayHasKey('expires_in', $fragment);
        $this->assertEqualsWithDelta(3600, $fragment['expires_in'], 1.0);
    }
}
