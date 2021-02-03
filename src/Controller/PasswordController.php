<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ChangePasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class PasswordController extends AbstractController
{
    /**
     * @Route("/password", name="password")
     */
    public function index(): Response
    {
        return $this->render('password/index.html.twig', [
            'controller_name' => 'PasswordController',
        ]);
    }

    /**
     * @Route ("/request", name="app_forgot_password_request")
     */

    public function request (Request $request,UserRepository $userRepository,
                             UserPasswordEncoderInterface $passwordEncoder,
                             \Swift_Mailer $mailer, EntityManagerInterface $em): Response
    {
        $random = random_int(1,10000000);



       if( $utilisateur=$userRepository->findOneBy(['email'=>$request->request->get('mail')])){

 $utilsateurpseudo=$utilisateur->getUsername();
$this->em=$em;

$utilisateur1=$utilisateur->getEmail();
        if($utilisateur1 ){
            // ici nous enverrons le mail
            $message= (new \Swift_Message('Réinitialisation du mot de passe'))
                ->setFrom('votre@adresse.fr')
                ->setTo($utilisateur1)
                ->setBody(
                    $this->renderView(
                        'emails/change_mdp.html.twig',[
                            'random'=>$random,
                            'utilisateurpseudo'=>$utilsateurpseudo,
                           ]
                    ),'text/html'
                )
            ;
                $mailer->send($message);
                $encodePassword = $passwordEncoder->encodePassword($utilisateur,$random);
                $utilisateur->setPassword($encodePassword);
                $this->em->flush();

dump($message);
            return $this->redirectToRoute('home1', ['id'=>$utilisateur->getId()]);
        }elseif ($request->isMethod('POST')) {
            $this->redirectToRoute('app_login');


        }}else
        return $this->render('user/reset_password/request.html.twig');

    }




    /**
     * @Route("/reset/{id}", name="app_reset_password", requirements={"id": "\d+"})
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                          User $utilisateur, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");


        $this->em=$em;
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $utilisateur,
                $form->get('plainPassword')->getData()
            );

            $utilisateur->setPassword($encodedPassword);
            $this->em->flush();

           // $this->flashy->success('Votre mot de passe a bien été réinitialisé !');
            return $this->redirectToRoute('activite_index');
        }

        return $this->render('user/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'utilisateur'=>$utilisateur,
        ]);
    }

}
