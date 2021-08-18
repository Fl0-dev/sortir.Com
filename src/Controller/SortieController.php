<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieFormType;
use App\Repository\SortieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name = "sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/afficher", name = "afficher")
     */


    public function index() : Response
    {
        return $this->render('sortie/afficher.html.twig');


    }


    /**
     * @Route("/ajouter", name ="ajouter")
     * @param Request $request
     * @return Response
     */

    public function Ajouter(Request $request ): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setDateHeureDebut(new DateTime());
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render
        ('sortie/ajouter.html.twig',
            ['formSortie' => $form->createView()]);
    }

    /**
     * @Route("/{id}/modifier/", name ="modifier")
     */
    public function Modifier(Sortie $sortie, Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/modifier.html.twig',
            ['formSortie' => $form->createView()]);
    }
    /**
     * @Route("/afficher", name ="afficher")
     */




}
