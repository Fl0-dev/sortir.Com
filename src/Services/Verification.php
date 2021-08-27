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
        $etats= $this->etatRepository->findAll();
        $etatCloture = $etats[2];//$this->etatRepository->find(3);
        $etatEnCours = $etats[3];//$this->etatRepository->find(4);
        $etatPassee = $etats[4];//$this->etatRepository->find(5);

        $etatArchivee = $etats[6];//$this->etatRepository->find(7);


        //pour chaque sortie
        foreach ($sorties as $sortie){

            $sortieDateDebut = $sortie->getDateHeureDebut();
            //clonage de la variable pour éviter de toucher à la variable $dateHeureDebut
            $dateFinsortie = clone $sortieDateDebut;
            $interval = new DateInterval('PT'.$sortie->getDuree(). 'M');
            $dateFinsortie->add($interval);

            //si état ouvert
            if ($sortie->getEtat()->getId()==2) {
                //si date d'insciption > date d'aujourd'hui
                if($sortie->getDateLimiteInscription()<$today || $sortie->getNbInscriptionsMax()===count($sortie->getUsers())){
                    $sortie->setEtat($etatCloture);

                }
                //si date aujourdhui > à la sortie
                if($sortie->getDateHeureDebut()<$today){
                    //sortie débutée
                    $sortie->setEtat($etatEnCours);
                    //calcul de la fin de la sortie

                    //si fin de sortie > à aujourd'hui
                    if ($dateFinsortie<$today){
                        //sortie passée
                        $sortie->setEtat($etatPassee);
                    }
                }

            }
            //pour les sorties qui se sont finit depuis 1 mois minimum
            $dateFinSortiePlusTrente = $dateFinsortie->add(new DateInterval('P1M'));
            if ($dateFinSortiePlusTrente < $today){

                $sortie->setEtat($etatArchivee);
            }
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }

    }

    /**
     * permet de modifier les sorties d'un user
     * @param $user
     */
    public function gestionSortiesSelonEtatUser($user){
        //si user organisateur->annulation
        //récupération des sorties organisées
        $sortiesOrganisees = $this->sortieRepository->findBy(['organisateur'=>$user]);
        if ($sortiesOrganisees){
            $etatAnnule = $this->etatRepository->find(5);
            foreach ($sortiesOrganisees as $sortie){
                $sortie->setEtat($etatAnnule);
                $sortie->setInfosAnnulation("L'organisateur/trice ne fait plus parti du site");
                $this->entityManager->flush();
            }
        }
        //si user participant
        //récupération des sorties participées
        $sortiesParticipees = $this->sortieRepository->findSortiesParticipeesBy($user);
        if ($sortiesParticipees){
            $etatAnnule = $this->etatRepository->find(5);
            foreach ($sortiesParticipees as $sortie){
                $sortie->removeUser($user);
                $this->entityManager->flush();
            }
        }
    }
}