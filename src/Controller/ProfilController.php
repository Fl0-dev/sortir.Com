<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function modifier(User $user, Request $request, UserPasswordEncoderInterface $encoder):Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $mdp = $form->get('plainPassword')->getData();
            if($encoder->isPasswordValid($this->getUser(), $mdp)){
                $user->setPassword(
                    $encoder->encodePassword(
                        $user,
                        $form->get('new_password')->getData()
                    )
                );
                $em->flush();
                $this->addFlash('success', 'Les modifications ont bien été prise en compte!');
                return $this->redirectToRoute('accueil');
            }else{
                $form->get('plainPassword')->addError(new FormError('mot de passe erroné.'));
            }
        }
        return $this->render(
            'profil/modifier.html.twig',
            ['formUser' => $form->createView()]);

    }


}
