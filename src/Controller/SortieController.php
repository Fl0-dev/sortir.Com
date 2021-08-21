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
    public function ajouter(Request $request, EtatRepository $etatRepository): Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');
        // récupération de l'état
        $etatCreee = $etatRepository->find(1);
        $em = $this->getDoctrine()->getManager();
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setEtat($etatCreee);
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Todo OBLIGE CE FOUTU USER A RENTRER DES DATES ULTERIEURES A LA DATE DU JOUR
            //if ($this.date_diff())

            $em->persist($sortie);
            $em->flush();

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
    public function modifier(Sortie $sortie, Request $request) : Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

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






}
