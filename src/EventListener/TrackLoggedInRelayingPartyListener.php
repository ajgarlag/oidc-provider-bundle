<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\EventListener;

use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Event\IdTokenIssuedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TrackLoggedInRelayingPartyListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly \LoggedInRelayingPartyStorageInterface $loggedInRelayingPartyStorage,
    ) {
    }

    public function onTokenIssued(IdTokenIssuedEvent $event): void
    {
        $idToken = $event->getIdToken();

        $relayingParty = $idToken->getAuthorizedParty();
        if (null === $relayingParty) {
            return;
        }

        $this->loggedInRelayingPartyStorage->add($relayingParty);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IdTokenIssuedEvent::class => 'onTokenIssued',
        ];
    }
}
