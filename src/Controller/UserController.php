<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{#ce controlleur gère les pages user#
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository ): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        #liste tous les users
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        # creation d'un nouveau user#
        $user = new User();


        #utilisation du formulaire de l'entité user, on l'appelle ici#
        $form = $this->createForm(UserType::class, $user);

        #gere le traitement de la saisie du formulaire, on récupère les données depuis la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            #On recupere le role dans le formulaire,...
           $role= $user->getRoles(); dump($role[0]);
           $rolerecupere=$role[0];
            $role = [0 => $rolerecupere];

           # et on l'inscrit correctement en BDD
           $user->setRoles($role);

           #on recupere les coordonnées que le candidat a l'adhesion a envoyé
            $adherent = $form->getData();

            // on recupere les photos envoyées s'il y en a a l'inscription ou a la modification du profil
            $photos= $form->get('photos')->getData();

            //on boucle sur les photos
            foreach ($photos as $photo){

                // on genere un nouveau no de fichier photo
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                // copie le fichier dans le dossier photo-profil dans le 'public'
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $fichier
                );

                // on stocke le nom de la photo dans la bdd
                $phot= new Photo();
                $phot->setName($fichier);
                $user->addPhoto($phot);
            }

            $entityManager = $this->getDoctrine()->getManager();

            #on recupere le password non hashé pour l'envoyer en clair au candidat a l'inscription ds le mail
            $plainpassword=$user->getPassword();

            #hasher le mot de passe
            $hashed =$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);


            $entityManager->persist($user);
            $entityManager->flush();

            // ici nous enverrons le mail avec le mot de passe non hashé
            $message= (new \Swift_Message('Votre adhesion est validee'))
                ->setFrom('votre@dresse.fr')
                // on attribue le destinataire
                ->setTo($user->getEmail())

                // on créée le message avec la vue twig
                ->setBody(
                    $this->renderView(
                        'emails/buletin_valide.html.twig',[
                        'adherent'=>$adherent,
                         'user'=>$user,
                            'password'=>$plainpassword,]
                    ),'text/html'
                )
            ;

            // on envoie le message
            $mailer->send($message);




            $this->addFlash('success', 'Votre profil a bien été créé !!');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user, UserRepository $userRepository): Response
    {

        $this->denyAccessUnlessGranted("ROLE_USER");
        #on recupere le pseudo de l'adhérent en cours
        $user1=$this->getUser()->getUsername();
        $userrole=$this->getUser()->getRoles();
        #on recupere tout l'adhérent en cours
        $utilisateur=$userRepository->findOneBy(['username'=>$user]);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'user1'=> $user1,
            'username'=>$utilisateur,
            'userrole'=>$userrole,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        #on recupere le pseudo de l'adhérent en cours
        $user1=$this->getUser()->getUsername();

        #utilisation du formulaire de la classe user#
        $form = $this->createForm(UserType::class, $user);

       #gere le traitement du formulaire#
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            #hasher le mot de passe
            $hashed =$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);

            // on recupere les photos envoyées
            $photos= $form->get('photos')->getData();
dump($photos);
            //on boucle sur les photos
            foreach ($photos as $photo){

                // on genere un nouveau no de fichier
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();
dump($fichier);
                // copie le fichier dans le dossier photo-profil
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $fichier
                );

                // on stocke le nom de la photo dans la bdd
                $phot= new Photo();
                $phot->setName($fichier);
                $user->addPhoto($phot);
            }


            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié !!');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'user1'=>$user1
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user, PhotoRepository $photoRepository): Response

    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        #protection contre les attaques csrf#
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
$user1=$user->getId();
            $photo=$photoRepository->findOneBy(['adhherent'=>$user1]);
dump($photo);
if($photo != null) {
    $nomphoto = $photo->getName();
    $photoexist=$this->getParameter('photo_directory') . '/' . $nomphoto;
    dump($photoexist);
    if($photoexist ) {
        unlink($photoexist);
    }
    else{}
}

                // On récupère le nom de l'image
               // $nom = $photo->getName();
                // On supprime le fichier
               // unlink($this->getParameter('photo_directory').'/'.$nom);


                $entityManager->remove($user);
            $entityManager->flush();


        }
        $this->addFlash('success', 'Votre profil a bien été effacé !!');

        return $this->redirectToRoute('user_index');

    }


    /**
     * @Route("/supprime/photo/{id}", name="user_delete_photo", methods={"DELETE"})
     */
    public function deleteImage(Photo $photo, Request $request){

        $this->denyAccessUnlessGranted("ROLE_USER");

        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$photo->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $photo->getName();
            // On supprime le fichier
            unlink($this->getParameter('photo_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
        return $this->redirectToRoute('user_index');
    }

/**
 * @Route ("/profiledit/{id}", name="profiledit")
 */

    public function profiledit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        #on recupere le pseudo de l'adhérent en cours
        $user1=$this->getUser()->getUsername();

        #utilisation du formulaire de la classe user#
        $form = $this->createForm(UserType::class, $user);

        #gere le traitement du formulaire#
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            #hasher le mot de passe
            $hashed =$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);

            // on recupere les photos envoyées
            $photos= $form->get('photos')->getData();
dump($photos);
            //on boucle sur les photos
            foreach ($photos as $photo){

                // on genere un nouveau no de fichier
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();
dump($fichier);
                // copie le fichier dans le dossier photo-profil
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $fichier
                );

                // on stocke le nom de la photo dans la bdd
                $phot= new Photo();
                $phot->setName($fichier);
                $user->addPhoto($phot);
            }


            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié !!');
            return $this->redirectToRoute('home1');
        }

        return $this->render('user/profiledit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'user1'=>$user1,
        ]);
    }




}


