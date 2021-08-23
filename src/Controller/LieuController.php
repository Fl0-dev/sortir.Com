<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu", name="lieu_")
 */
class LieuController extends AbstractController
{

    /**
     * @Route("/ajouter/{route}/{id}", name="ajouter")
     */
    public function ajouterLieu($id,$route,Request $request,EntityManagerInterface $entityManager): Response
    {

        $lieu = new Lieu();
        //création du form de LieuType
        $lieuForm = $this->createForm(LieuType::class,$lieu);
        //retour de la requête
        $lieuForm->handleRequest($request);

        //si form soumis et valide
        if ($lieuForm->isSubmitted()&&$lieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            //message flash
            $this->addFlash('success','Ce lieu a été bien ajouté ! Merci');
            //redirection selon d'où on vient
            return $this->redirectToRoute($route,['id'=>$id]);

        }
        return $this->render('lieu/ajouter.html.twig',[
           'lieuForm'=>$lieuForm->createView(),
        ]);
    }
}
