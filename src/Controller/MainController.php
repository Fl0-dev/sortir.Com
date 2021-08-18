<?php

namespace App\Controller;

use App\Entity\RechercheSortie;
use App\Form\RechercheSortieType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function list(SortieRepository $sortieRepository):Response
    {
        $rechercheSortie = new RechercheSortie();
        //liste des sorties sans recherche
        $sorties = $sortieRepository->findBy([],["dateHeureDebut"=>"ASC"]);
        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class,$rechercheSortie);

        return $this->render('main/accueil.html.twig',[
            "sorties"=>$sorties,
            "sortieForm"=>$sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/accueil/recherche", name="recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request, UserRepository $userRepository):Response
    {
        //initialisation de l'instance des resultats du form
        $rechercheSortie = new RechercheSortie();

        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class,$rechercheSortie);
        // retour de la réponse
        $sortieForm->handleRequest($request);
        //si form soumis et valide

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //hydratation pour recherche
            $campus = $rechercheSortie->getCampus();
            $text = $rechercheSortie->getText();
            //récupération des champs mapped=>false
            $dateDebut= $sortieForm->get('dateDebut')->getData();
            $dateFin= $sortieForm->get('dateFin')->getData();
            //traitement des dates si null
            if($dateDebut==null){
                //date du jour
                $rechercheSortie->setDateDebut(new \DateTime());
            }
            if($dateFin==null){
                //date du jour + 1 mois
                $rechercheSortie->setDateFin((new \DateTime())->modify('+1 month'));
            }
            $organise =$rechercheSortie->isOrganisateur();
            $inscrit = $rechercheSortie->isInscrit();
            $nonInscrit = $rechercheSortie->isNonInscrit();
            $sortiesPassees = $rechercheSortie->isSortiesPassees();


            //TODO:recherches persos
            $sorties = $sortieRepository->findByPerso($campus,$text,$rechercheSortie->getDateDebut(),$rechercheSortie->getDateFin(),
                $organise, $inscrit,$nonInscrit,$sortiesPassees);
            //TODO:return la recherche

        }else{
            //liste des sorties sans recherche
            $sorties = $sortieRepository->findBy([],["dateHeureDebut"=>"DESC"]);
        }
        return $this->render('main/accueil.html.twig',[
            "sorties"=>$sorties,
            "sortieForm"=>$sortieForm->createView(),
        ]);
    }

}
