<?php

namespace App\Services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class Verification


{
    private  $sortieRepository;
    private  $entityManager;
    private  $etatRepository;

    public function __construct(SortieRepository $sortieRepository,EntityManagerInterface $entityManager,EtatRepository $etatRepository){
        $this->sortieRepository =$sortieRepository;
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
    }
    /**
     * renvoie un bool si l'user est dans le groupe des participants
     * @param $sortie
     * @param $user
     * @return bool
     */
    public function verifUserInscrit($sortie,$user){
        $inscrit =false;
        $userId =$user->getId();
        $groupe=$sortie->getUsers();
        foreach ($groupe as $personne){
            if ($userId ==$personne->getId()){
                $inscrit = true;
            }
            break;
        }
        return $inscrit;
    }

    /**
     * verifie l'état des sortie par rapport aux dates et le change si besoin
     *
     */
    public function gestionEtatSorties(){

        //date du jour
        $today = new \DateTime();

        //récupération de toutes les sorties
        $sorties = $this->sortieRepository->findBy([], ["dateHeureDebut" => "ASC"]);
        //récupération des états voulus
        $etatCloture = $this->etatRepository->find(3);
        $etatEnCours = $this->etatRepository->find(4);
        $etatPassee = $this->etatRepository->find(5);
        $etatArchivee = $this->etatRepository->find(7);


        //pour chaque sortie
        foreach ($sorties as $sortie){
            $dateFinsortie = ($sortie->getDateHeureDebut())->add(new DateInterval('PT'.$sortie->getDuree(). 'M'));
            //si état ouvert
            if ($sortie->getEtat()->getId()==2) {
                //si date d'insciption > date d'aujourd'hui
                if($sortie->getDateLimiteInscription()>$today){
                    $sortie->setEtat($etatCloture);

                }
                //si date aujourdhui > à la sortie
                if($sortie->getDateHeureDebut()>$today){
                    //sortie débutée
                    $sortie->setEtat($etatEnCours);
                    //calcul de la fin de la sortie

                    //si fin de sortie > à aujourd'hui
                    if ($dateFinsortie>$today){
                        //sortie passée
                        $sortie->setEtat($etatPassee);
                    }
                }

            }
            //pour les sorties qui se sont finit depuis 30 jours minimum
            $dateFinSortiePlusTrente = $dateFinsortie->modify('+ 1 month');
            if ($dateFinSortiePlusTrente > $today){
                $sortie->setEtat($etatArchivee);
            }
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }

    }
}