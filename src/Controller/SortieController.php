<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/sortie")
     * @param Request $request
     * @return Response
     */

    public function Ajouter(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = new Sortie();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setDateCreated(new \DateTime());
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('accueil');

        }

        return $this->render
        ('ajouter.sortie.html.twig',
            ['formSortie' => $form->createView()]);
    }
}
