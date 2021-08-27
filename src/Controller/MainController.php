<?php

namespace App\Controller;

use App\Entity\RechercheSortie;
use App\Entity\Sortie;
use App\Form\RechercheSortieType;

use App\Repository\SortieRepository;
use App\Services\Verification;

use Doctrine\ORM\EntityManagerInterface;

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
    public function list(SortieRepository $sortieRepository,Verification $verification): Response
    {
        $user = $this->getUser();
        if($user->getEtat()==false){
            return $this->render('bundles/TwigBundle/Exception/inactifUser.html.twig');
        }

        //traitement des états des sorties
        $verification->gestionEtatSorties();
        $rechercheSortie = new RechercheSortie();
        //liste des sorties sans recherche
        $sorties = $sortieRepository->findSansLesArchives();
        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class, $rechercheSortie);

        return $this->render('main/accueil.html.twig', [
            "sorties" => $sorties,
            "sortieForm" => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/accueil/recherche", name="recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request,Verification $verification): Response
    {
        //traitement des états des sorties
        $verification->gestionEtatSorties();
        //initialisation de l'instance des resultats du form
        $rechercheSortie = new RechercheSortie();
        //récupération de l'user connecté
        $user = $this->getUser();
        //mise en route du du formulaire de recherche
        $sortieForm = $this->createForm(RechercheSortieType::class, $rechercheSortie);
        // retour de la réponse
        $sortieForm->handleRequest($request);
        //si form soumis et valide

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {


            //récupération pour recherche
            $campus = $rechercheSortie->getCampus();
            $text = $rechercheSortie->getText();
            $organise = $rechercheSortie->isOrganise();
            $inscrit = $rechercheSortie->isInscrit();
            $nonInscrit = $rechercheSortie->isNonInscrit();
            $sortiesPassees = $rechercheSortie->isSortiesPassees();
            //récupération des champs mapped=>false
            $dateDebut = $sortieForm->get('dateDebut')->getData();

            $dateFin = $sortieForm->get('dateFin')->getData();
            //traitement des dates si null

            if ($dateDebut == null) {
                //date du jour
                $dateDebut = ((new \DateTime())->modify('-1 month'));
            }
            if ($dateFin == null) {
                //date du jour + 1 mois
                $dateFin = ((new \DateTime())->modify('+1 month'));
            }

            //utilisation d'une fonction perso pour récupérer les sorties en fonction des données de recherche
            $sorties = $sortieRepository->findByPerso($campus, $text, $dateDebut, $dateFin,
                $organise, $inscrit, $nonInscrit, $sortiesPassees, $user);
        } else {
            //liste des sorties sans recherche
            $sorties = $sortieRepository->findSansLesArchives();
        }
        return $this->render('main/accueil.html.twig', [
            "sorties" => $sorties,
            "sortieForm" => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/accueil/inscription/{id}", name="inscription")
     */
    public function inscription(Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository): Response
    {
        //recupération de l'User connecté
        $user = $this->getUser();
        $inscrit =$sortieRepository->findInscrit($user);

        //vérifier si sortie est à l'état : ouverte, si il y a encore de la place et si pas déjà inscrit
        if (($sortie->getEtat()->getId() == '2') && ($sortie->getNbInscriptionsMax() > count($sortie->getUsers()))&&!$inscrit) {
            //ajout du user dans la sortie
            $sortie->addUser($user);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Tu es inscrit pour la sortie : ' . $sortie->getNom());
        }
        else{
            $this->addFlash('danger', "Tu n'as pas réussi à t'inscrire pour la sortie : " . $sortie->getNom());
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/accueil/desinscription/{id}",name="desinscription")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function desincription(Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository):Response
    {
        //recupération de l'User connecté
        $user = $this->getUser();
        $inscrit= $sortieRepository->findInscrit($user);


        //vérifier si sortie toujours ouverte et si déjà inscrit
        if ($sortie->getEtat()->getId() == 2 && $inscrit) {
            $sortie->removeUser($user);
            $entityManager->flush();
            $this->addFlash('success', 'Tu as bien annulé ton inscription à la sortie : ' . $sortie->getNom());
        }
        else{
            $this->addFlash('danger', "Tu n'as pas réussi à annuler ton inscription pour la sortie : " . $sortie->getNom());
        }
        return $this->redirectToRoute('accueil');
    }

}
