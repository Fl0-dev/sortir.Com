<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationUserParFichierCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private SymfonyStyle $io;
    protected static $defaultName = 'creationUserParFichier';
    protected static $defaultDescription = 'Importer des données grâce à un fichier';

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input,$output);
    }

    /*private function getDataFromFile(): array{

    }*/

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();

        return Command::SUCCESS;
    }
    private function createUsers(): void{

    }
}
