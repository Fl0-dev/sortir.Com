<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnulationType;
use App\Form\ImportCsvType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Services\Verification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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


    /**
     * @Route ("accueil/gestionUsers/ajouterUserParFichier", name="ajouterParFichier")
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function ajouterUserParFichier(EntityManagerInterface $entityManager,
                                          UserPasswordEncoderInterface $passwordEncoder,
                                          UserRepository $userRepository,
                                          Request $request): Response
    {

        $form = $this->createForm(ImportCsvType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $dossierFichier */
            $fichier = $form->get('fichier')->getData();
            if ($fichier) {
                $normalizers = [new ObjectNormalizer()];
                //permet de mettre une donnée en objet
                $encoders = [
                    new CsvEncoder(),
                ];
                //création du serializer qui fera la conversion
                $serializer = new Serializer($normalizers, $encoders);
                //mise en string du contenu du fichier
                $fileString = file_get_contents($fichier);
                //récupération grâce au serializer du contenu dans un tableau
                $data = $serializer->decode($fileString, $fichier->getClientOriginalExtension());
                //si on a bien un tableau
                if ($data) {
                    //connaître le nombre d'users créés
                    $userCreated = 0;
                    //boucler sur le tableau retourner par la fonction
                    foreach ($data as $row) {
                        //vérif si pas déjà dans la BD par l'email et par le pseudo
                        if (array_key_exists('email', $row) && !empty($row['email'])) {
                            $user = $userRepository->findOneBy([
                                'email' => $row['email']
                            ]);

                        } elseif (array_key_exists('pseudo', $row) && !empty($row['pseudo'])) {
                            $user = $userRepository->findOneBy([
                                'pseudo' => $row['pseudo']
                            ]);
                        }

                            //si pas de user :
                            if (!$user) {
                                $user = new User();
                                $campus = new Campus();
                                //Hydratation d'un campus
                                $campus->setNom($row['campus']);
                                $entityManager->persist($campus);
                                //hydratation d'un user avec
                                $user->setEmail($row['email'])
                                    ->setPassword($passwordEncoder->encodePassword($user,'password'))
                                    ->setNom($row['nom'])
                                    ->setPrenom($row['prenom'])
                                    ->setPseudo($row['pseudo'])
                                    ->setCampus($campus)
                                    ->setRoles(["ROLE_USER"])
                                    ->setEtat(true);
                                $entityManager->persist($user);
                                //on incrémente pour chaque création
                                $userCreated++;

                            }

                    }
                    $entityManager->flush();
                    if ($userCreated > 1) {
                        $string = $userCreated . "utilisateurs/trices créés en BD";
                    } elseif ($userCreated === 1) {
                        $string = "1 utilisateur/trice a été créé en DB";
                    } else {
                        $string = "aucun utilisateur/trice n'a été créé en DB";
                    }

                    $this->addFlash('success', $string);
                    return $this->redirectToRoute('admin_gestionUsers');
                }

            }

        }
        return $this->render(
            'admin/ajoutCsv.html.twig',
            ['formUser' => $form->createView(),
            ]);
    }
    /**
     * @Route("accueil/gestionSortie", name="gestionSortie")
     */
    public function gestionSortie (SortieRepository $sortieRepository) : Response
    {
        // récupération de la liste des sorties
        $sorties = $sortieRepository->findAll();

        return $this->render('admin/gestionSortie.html.twig', [
            'sorties'=>$sorties
        ]);
    }
    /**
     * @Route("/{id}/annuler", name="annuler")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annuler(Sortie $sortie, EntityManagerInterface $entityManager, Request $request, EtatRepository $etatRepository): Response
    {
        $annulationForm = $this->createForm(AnnulationType::class, $sortie);
        $annulationForm->handleRequest($request);
        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {


            $etatAnnulee = $etatRepository->find(2);
            $sortie->setEtat($etatAnnulee);
            $entityManager->flush();
            $this->addFlash('warning', 'La sortie a bien été annulée.');
            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/annuler.html.twig', [
            'annulationForm' => $annulationForm->createView(),
            'sortie' => $sortie,
        ]);
    }
}

