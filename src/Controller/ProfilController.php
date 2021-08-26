<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="user_profil")
     */
    public function profil(User $user): Response
    {
        $userApp = $this->getUser();
        if($userApp->getEtat()==false){
            return $this->render('bundles/TwigBundle/Exception/inactifUser.html.twig');
        }

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="user_modifier")
     */
    public function modifier(User $user, Request $request, UserPasswordEncoderInterface $encoder,SluggerInterface $slugger):Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //gestion de la photo
            /** @var UploadedFile $dossierPhotos */
            $dossierPhotos = $form->get('photo')->getData();
            if ($dossierPhotos){
                $nomOriginalDeFichier = pathinfo($dossierPhotos->getClientOriginalName(), PATHINFO_FILENAME);
                //on change le nom du fichier
                $nomDeFichierSecur = $slugger->slug($nomOriginalDeFichier);
                $nomDeFichier = $nomDeFichierSecur.'-'.uniqid().'.'.$dossierPhotos->guessExtension();
                try{
                    $dossierPhotos->move(
                        $this->getParameter('photo_dossier'),
                        $nomDeFichier
                    );
                }catch (FileException $e){
                    $this->addFlash('error',"Soucis lors de l'enregistrement. Désolé");
                }
                $user->setPhoto($nomDeFichier);

            }

            //gestion des password
            $mdp = $form->get('plainPassword')->getData();
            //si ancien password et user pseudo/email est bon
            if($encoder->isPasswordValid($this->getUser(), $mdp)){
                //et si quelque chose est noté dans le champ new_password
                //on change le password en BD
                if($form->get('new_password')->getData()) {
                    $user->setPassword(
                        $encoder->encodePassword(
                            $user,
                            $form->get('new_password')->getData()
                        )
                    );
                }
                $em->flush();
                $this->addFlash('success', 'Les modifications ont bien été prise en compte!');
                return $this->redirectToRoute('user_profil',['id'=>$user->getId()]);
            }else{
                $form->get('plainPassword')->addError(new FormError('mot de passe erroné.'));
            }
        }
        return $this->render(
            'profil/modifier.html.twig',
            ['formUser' => $form->createView(),
                'user'=>$user,
                ]);

    }


}
