<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="user_profil")
     */
    public function profil(User $user): Response
    {
        return $this->render('profil/profil.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="user_modifier")
     */
    public function modifier(User $user, Request $request):Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {

            $em->flush();
            //TO DO message flash confirmation modification reussie
            return $this->redirectToRoute('accueil');
        }
        return $this->render(
            'profil/modifier.html.twig',
            ['formUser' => $form->createView()]);

    }


}
