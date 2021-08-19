<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\SortieRepository;

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
     */
    public function verifEtatSortie(SortieRepository $sortieRepository){

        //si etat ouvert ou cloturé :

        //vérification par rapport à aujourd'hui

        //si date = aujourd'hui

            //si heure >heure de début
                //calcul du temps avant fin d'activité

                // si inférieure ->état débuté




    }
}