<?php

namespace App\Command;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:check-setup')]
class CheckSetupCommand extends Command
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure(): void
    {
        $this->setDescription('Check DB setup and required tables (gestionnaires)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $conn = $this->doctrine->getConnection();
            $result = $conn->fetchOne('SELECT 1 FROM gestionnaires LIMIT 1');
            $io->success('Table "gestionnaires" exists.');
            return Command::SUCCESS;
        } catch (TableNotFoundException $e) {
            $io->error('Missing table: "gestionnaires". Please run migrations: php bin/console doctrine:migrations:migrate');
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('Unexpected error while checking DB: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
