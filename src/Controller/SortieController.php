<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieFormType;

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


    public function afficher(Sortie $sortie) : Response

    {

        return $this->render('sortie/afficher.html.twig', [
            "sortie"=>$sortie
        ]);



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

            if ($this.date_diff())

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('sucess', 'Sortie crée !');
            return $this->redirectToRoute('accueil');

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
     * @Route("/annuler/{id}", name ="annuler")
     */

    public function annuler(Sortie $sortie, EntityManagerInterface $em) : Response
    {

        $em->remove($sortie);
        $em->flush();
        return $this->redirectToRoute('accueil');
        }






}
