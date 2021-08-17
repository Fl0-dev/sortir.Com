<?php

namespace App\Controller;

use App\Form\RechercheSortieType;
use App\Repository\SortieRepository;
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
        //liste des sorties sans recherche
        $sorties = $sortieRepository->findBy([],["dateHeureDebut"=>"DESC"]);
        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class);

        return $this->render('main/accueil.html.twig',[
            "sorties"=>$sorties,
            "sortieForm"=>$sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/accueil/recherche", name="recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request):Response
    {
        //liste des sorties sans recherche
        $sorties = $sortieRepository->findBy([],["dateHeureDebut"=>"DESC"]);
        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class);
        // retour de la réponse
        $sortieForm->handleRequest($request);
        //si form soumis et valide
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            //TODO:hydratation pour recherche
            //TODO:recherches persos
            //TODO:return la recherche
            return $this->redirectToRoute('accueil',[],Response::HTTP_SEE_OTHER);
        }
        return $this->render('main/accueil.html.twig',[
            "sorties"=>$sorties,
            "sortieForm"=>$sortieForm,
        ]);
    }

}
