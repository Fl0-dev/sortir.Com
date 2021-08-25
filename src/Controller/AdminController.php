<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Verification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("accueil/", name="accueil")
     */
    public function accueilAdmin(): Response
    {
        //récupération de l'user connecté
        $user = $this->getUser();
        return $this->render('admin/accueilAdmin.html.twig',[
            'user'=>$user,
        ]);
    }

    /**
     * @Route("accueil/gestionUsers", name="gestionUsers")
     */
    public function gestionUsers(UserRepository $userRepository): Response
    {
        //récupération de la listes des users
        $users = $userRepository->findAll();

        return $this->render('admin/gestionUsers.html.twig', [
            'users'=>$users,
        ]);
    }

    /**
     * @Route("accueil/gestionUsers/modifierRole/{id}", name="modifierRole")
     */
    public function modifierRole(User $user,EntityManagerInterface $entityManager): Response
    {
        $userRoles = $user->getRoles();
        //si 2 rôles dans le tableau enlever
        if (in_array('ROLE_USER',$userRoles)) {
            $user->setRoles(["ROLE_ADMIN"]);
        }
        if (in_array('ROLE_ADMIN',$userRoles)){
            $user->setRoles(['ROLE_USER']);
        }
        $entityManager->flush();
        return $this->redirectToRoute('admin_gestionUsers');
    }

    /**
     * @Route("accueil/gestionUsers/supprimer/{id}", name="supprimer")
     */
    public function supprimerUser(User $user,EntityManagerInterface $entityManager,Verification $verification): Response
    {
        //gestion des sorties
        $verification->gestionSortiesSelonEtatUser($user);
        //si organisateur->annuler les sorties
        //si participants->enlever de la sortie
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_gestionUsers');
    }

    /**
     * @Route("accueil/gestionUsers/etatUser/{id}", name="etatUser")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function etatUser(User $user,EntityManagerInterface $entityManager,Verification $verification): Response
    {
        //si organisateur->annuler les sorties
        //si participants->enlever de la sortie
        $userRoles = $user->getRoles();
        //si 2 rôles dans le tableau enlever
        if (in_array('ROLE_USER',$userRoles)) {
            //gestion des sorties
            $verification->gestionSortiesSelonEtatUser($user);
            $user->setRoles([""]);
            $user->setEtat(false);
        }else{
            $user->setRoles(["ROLE_USER"]);
            $user->setEtat(true);
        }
        $entityManager->flush();
        return $this->redirectToRoute('admin_gestionUsers');
    }
}

