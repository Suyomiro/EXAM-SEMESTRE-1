<?php

namespace App\Command;

use App\Entity\Gestionnaire;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:hash-gestionnaire-passwords')]
class HashGestionnairePasswordsCommand extends Command
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure(): void
    {
        $this->setDescription('Hash plain-text passwords for all gestionnaires using password_hash(PASSWORD_DEFAULT)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->doctrine->getManager();
        $repo = $em->getRepository(Gestionnaire::class);

        $gestionnaires = $repo->findAll();
        $updated = 0;

        foreach ($gestionnaires as $g) {
            $pw = $g->getPassword();
            // Quick heuristic: if it doesn't look like a hash (doesn't start with $), re-hash it
            if (!is_string($pw) || $pw === '' || $pw[0] !== '$') {
                $newHash = password_hash((string) $pw, PASSWORD_DEFAULT);
                $g->setPassword($newHash);
                $em->persist($g);
                $updated++;
            }
        }

        if ($updated > 0) {
            $em->flush();
            $io->success("Hashed passwords for {$updated} gestionnaire(s).");
        } else {
            $io->success('No plain-text passwords found; nothing to do.');
        }

        return Command::SUCCESS;
    }
}
