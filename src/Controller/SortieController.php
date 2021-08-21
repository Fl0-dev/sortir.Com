<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnulationType;
use App\Form\SortieFormType;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

/**
 * @Route("/sortie", name = "sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/afficher/{id}", name = "afficher")
     */
    public function afficher(Sortie $sortie): Response
    {
        return $this->render('sortie/afficher.html.twig', [
            "sortie" => $sortie
        ]);
    }


    /**
     * @Route("/ajouter", name ="ajouter")
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function ajouter(Request $request,
                            EtatRepository $etatRepository,
                            EntityManagerInterface $entityManager): Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');
        // récupération de l'état
        $etats = $etatRepository->findAll();
        //création de l'ojet sortie
        $sortie = new Sortie();
        //récupération de l'user
        $sortie->setOrganisateur($this->getUser());
        //Utilisation du form de sortie
        $form = $this->createForm(SortieFormType::class, $sortie);
        //et envoie du forme en requête
        $form->handleRequest($request);
        //si valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si l'user veut que la sortie soit créée
            if ($request->get("choix")==="enregistre") {
                $sortie->setEtat($etats[0]);
            }
            //Si l'user veut qu'elle soit publier direct
            if ($request->get("choix")==="publie") {
                $sortie->setEtat($etats[1]);
            }
            //ajout de l'user dans la sortie
            $sortie->addUser($this->getUser());
            //if ($this.date_diff())

            //on inscrit en BD
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie crée !');
            return $this->redirectToRoute('accueil');

        }

        return $this->render
        ('sortie/ajouter.html.twig', [
            'route'=>$routeName,
            'formSortie' => $form->createView(),
            ]);
    }


    /**
     * @Route("/{id}/modifier/", name ="modifier")
     */
    public function modifier(Sortie $sortie,
                             Request $request,
                             EtatRepository $etatRepository,
                             EntityManagerInterface $entityManager) : Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');
        // récupération de l'état
        $etats = $etatRepository->findAll();
        //Utilisation du form de sortie
        $form = $this->createForm(SortieFormType::class, $sortie);
        //et envoie du forme en requête
        $form->handleRequest($request);
        //si valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si l'user veut que la sortie soit créée
            if ($request->get("choix")==="enregistre") {
                $sortie->setEtat($etats[0]);
            }
            //Si l'user veut qu'elle soit publier direct
            if ($request->get("choix")==="publie") {
                $sortie->setEtat($etats[1]);
            }
            //on inscrit en BD
            $entityManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/modifier.html.twig', [
            'sortie'=>$sortie,
            'route'=>$routeName,
            'formSortie' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/supprimer/", name ="supprimer")
     */
    public function supprimer(Sortie $sortie, EntityManagerInterface $em) : Response
    {
        $em->remove($sortie);
        $em->flush();
        return $this->redirectToRoute("accueil");
    }

    /**
     * si déjà publier, annule la sortie en changeant son état
     * @Route("/{id}/annuler", name="annuler")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annuler(Sortie $sortie,EntityManagerInterface $entityManager, Request $request,EtatRepository $etatRepository): Response
    {
        $annulationForm = $this->createForm(AnnulationType::class,$sortie);
        $annulationForm->handleRequest($request);
        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {


            $etatAnnulee = $etatRepository->find(6);
            $sortie->setEtat($etatAnnulee);
            $entityManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/annuler.html.twig',[
            'annulationForm'=>$annulationForm->createView(),
            'sortie'=>$sortie,
        ]);
    }

    /**
     * @Route("{id}/publier", name="publier")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function publierDirect(Sortie $sortie,
                                  EntityManagerInterface $entityManager,
                                  EtatRepository $etatRepository):Response
    {
        $etatOuverte = $etatRepository->find(2);
        $sortie->setEtat($etatOuverte);
        $entityManager->flush();
        return $this->redirectToRoute('accueil');

    }



}
