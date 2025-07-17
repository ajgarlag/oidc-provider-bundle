<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\Command;

use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Manager\RelyingPartyManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'ajgarlag:openid-connect-provider:save-relying-party', description: 'Saves an OpenID Connect relying party')]
final class SaveRelyingPartyCommand extends Command
{
    public function __construct(
        private readonly ClientManagerInterface $clientManager,
        private readonly RelyingPartyManagerInterface $relyingPartyManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Saves an OpenID Connect relying party')

            ->addOption('add-post-logout-redirect-uri', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add allowed post logout redirect uri to the client.', [])
            ->addOption('remove-post-logout-redirect-uri', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove allowed post logout redirect uri to the client.', [])

            ->addOption('set-front-channel-logout-uri', null, InputOption::VALUE_OPTIONAL, 'Set front channel logout uri.', null)
            ->addOption('unset-front-channel-logout-uri', null, InputOption::VALUE_NONE, 'Unset front channel logout uri.')

            ->addArgument('identifier', InputArgument::REQUIRED, 'The client identifier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (null === $client = $this->clientManager->find($input->getArgument('identifier'))) {
            $io->error(\sprintf('OAuth2 client identified as "%s" does not exist.', $input->getArgument('identifier')));

            return 1;
        }

        if ($input->getOption('set-front-channel-logout-uri') && $input->getOption('unset-front-channel-logout-uri')) {
            $io->error('Cannot specify "--set-front-channel-logout-uri" and "--unset-front-channel-logout-uri" at the same time.');

            return 2;
        }

        $relyingParty = $this->relyingPartyManager->get($client);

        $relyingParty->setPostLogoutRedirectUris(...$this->getClientRelatedModelsFromInput($input, RedirectUri::class, $relyingParty->getPostLogoutRedirectUris(), 'post-logout-redirect-uri'));
        if ($input->getOption('unset-front-channel-logout-uri') || $frontChannelLogoutUri = $input->getOption('set-front-channel-logout-uri')) {
            $relyingParty->setFrontChannelLogoutUri($frontChannelLogoutUri ?? null);
        }

        $this->relyingPartyManager->save($relyingParty);

        $io->success('OpenID Connect relying party saved successfully.');

        return 0;
    }

    /**
     * @template T of RedirectUri
     *
     * @param list<\Stringable> $actual
     * @param class-string<T> $modelFqcn
     *
     * @return list<T>
     */
    private function getClientRelatedModelsFromInput(InputInterface $input, string $modelFqcn, array $actual, string $argument): array
    {
        /** @var list<non-empty-string> $toAdd */
        $toAdd = $input->getOption($addArgument = \sprintf('add-%s', $argument));

        /** @var list<non-empty-string> $toRemove */
        $toRemove = $input->getOption($removeArgument = \sprintf('remove-%s', $argument));

        if ([] !== $colliding = array_intersect($toAdd, $toRemove)) {
            throw new \RuntimeException(\sprintf('Cannot specify "%s" in either "--%s" and "--%s".', implode('", "', $colliding), $addArgument, $removeArgument));
        }

        $filtered = array_filter($actual, static fn ($model): bool => !\in_array((string) $model, $toRemove));

        /** @var list<T> */
        return array_merge($filtered, array_map(static fn (string $value) => new $modelFqcn($value), $toAdd));
    }
}
