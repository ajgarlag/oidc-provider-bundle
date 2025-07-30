<?php

declare(strict_types=1);

interface LoggedInRelayingPartyStorageInterface
{
    /**
     * @param non-empty-string $clientId
     */
    public function add(string $clientId): void;

    /**
     * @return non-empty-string[]
     */
    public function all(): array;

    // public function clear(): void;
}
