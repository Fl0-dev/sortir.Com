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
     * @Route("/ajouter", name="ajouter")
     */
    public function ajouter(Request $request,EntityManagerInterface $entityManager): Response
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
            //TODO:: faire en sorte de choisir entre :
            // sortie_ajouter return $this->redirectToRoute('sortie_ajouter');
            // sortie_modifier return $this->redirectToRoute('sortie_modifier');
            return $this->redirect($request->server->get('HTTP_REFERER'));

        }
        return $this->render('lieu/ajouter.html.twig',[
           'lieuForm'=>$lieuForm->createView(),
        ]);
    }
}
