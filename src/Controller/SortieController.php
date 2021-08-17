<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
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
     * @Route("/ajouter", name ="ajouter")
     * @param Request $request
     * @return Response
     */

    public function Ajouter(Request $request ): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = new Sortie();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setDateHeureDebut(new DateTime());
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render
        ('sortie/ajouter.sortie.html.twig',
            ['formSortie' => $form->createView()]);
    }
}
