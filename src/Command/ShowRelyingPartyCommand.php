<?php

declare(strict_types=1);

namespace Ajgarlag\Bundle\OpenIDConnectProviderBundle\Command;

use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Manager\RelyingPartyManagerInterface;
use Ajgarlag\Bundle\OpenIDConnectProviderBundle\Model\RelyingPartyInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'ajgarlag:openid-connect-provider:show-relying-party', description: 'Show OpenID Connect relying party')]
final class ShowRelyingPartyCommand extends Command
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
            ->setDescription('Show OpenID Connect relying party')

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

        $relyingParty = $this->relyingPartyManager->get($client);

        $this->drawTable($io, $relyingParty);

        return 0;
    }

    private function drawTable(OutputStyle $io, RelyingPartyInterface $relyingParty): void
    {
        $columns = ['name', 'identifier', 'post logout redirect uri', 'front channel logout uri'];
        $rows = [array_combine($columns, [$relyingParty->getClient()->getName(), $relyingParty->getClient()->getIdentifier(), implode(', ', $relyingParty->getPostLogoutRedirectUris()), $relyingParty->getFrontChannelLogoutUri() ?? ''])];
        $io->table($columns, $rows);
    }
}
