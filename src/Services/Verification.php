<?php

namespace App\Services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class Verification
{
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
     * @param SortieRepository $sortieRepository
     * @param EntityManagerInterface $entityManager
     */
    public function verifEtatSortie(SortieRepository $sortieRepository,EntityManagerInterface $entityManager,
            EtatRepository $etatRepository){

        //date du jour
        $today = new \DateTime();

        //récupération de toutes les sorties
        $sorties = $sortieRepository->findAll();
        //récupération des états voulus
        $etatCloture = $etatRepository->find(3);

        //pour chaque sortie
        foreach ($sorties as $sortie){
            //si état ouvert
            if ($sortie->getEtat()->getId()==2) {
                //si date d'insciption > date d'aujourd'hui
                if($sortie->getDateLimiteInscription()>$today){
                    $sortie->setEtat($etatCloture);
                }
                //si date aujourdhui > activité
                if($sortie->getDateHeureDebut()>$today){
                    //si diff entre dateDuDebut et today inf à 0
                    $interval = date_diff($sortie->getDateHeureDebut() + $sortie->getDuree(), $today);
                }


                //vérification par rapport à aujourd'hui

                //si date = aujourd'hui

                //si heure >heure de début
                //calcul du temps avant fin d'activité

                // si inférieure ->état débuté

                // si supérieure ->état passé
            }
        }


    }
}