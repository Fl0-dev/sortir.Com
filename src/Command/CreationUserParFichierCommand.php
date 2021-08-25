<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CreationUserParFichierCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private UserRepository $userRepository;
    private SymfonyStyle $io;
    protected static $defaultName = 'app:create-users-from-file';
    protected static $defaultDescription = 'Importer des données grâce à un fichier';


    public function __construct(EntityManagerInterface $entityManager,string $dataDirectory,UserRepository $userRepository)
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository =$userRepository;
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input,$output);
    }

    private function getDataFromFile(): array{
        //récupération du fichier
        $file = $this->dataDirectory . 'random-users.csv';
        //récupération du type de fichier (extension)
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        //permet de mettre un objet en tableau
        $normalizers =[new ObjectNormalizer()];
        //permet de mettre une donnée en objet
        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
        ];
        //création du serializer qui fera la conversion
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers,$encoders);
        /** @var string $fileString */
        $fileString = file_get_contents($file);//mise en string du contenu du fichier
        //récupération grâce au serializer du contenu dans un tableau
        $data = $serializer->decode($fileString,$fileExtension);
        //si on a bien un tableau
        if (array_key_exists('results',$data)){
            //on retourne le tableau de résultats
            return $data['results'];
        }
        return $data;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();

        return Command::SUCCESS;
    }


    private function createUsers(): void{
        $this->getDataFromFile();
    }
}
