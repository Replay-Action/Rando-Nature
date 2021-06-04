<?php

namespace App\Controller;

use App\Form\AdhesionType;

use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class AdhesionController extends AbstractController
{
    /**
     * @Route("/adhesion", name="adhesion")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     *
     * Cette méthode permet d'envoyer un email avec les données saisie par l'utilisateur
     * pour qu'un admin puisse créer un adhérent avec les données.
     *
     */
    public function index(Request $request, Swift_Mailer $mailer): Response
    {
        //On créer notre formulaire.
        $form = $this->createForm(AdhesionType::class);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données saisie dans le formulaire et on lesstock dans la variable $adhesion.
            $adhesion = $form->getData();
            // envoi du mail.
            $message = (new Swift_Message('Nouvel Adherent'))

                // on attribue l'expediteur.
                ->setFrom($adhesion['email'])

                // on attribue le destinataire.
                ->setTo('vrnb2020@velorandonaturebruz.fr')

                // on créée le message avec le twig.
                ->setBody(
                    $this->renderView(
                        'emails/buletin_adhesion.html.twig', compact('adhesion')
                    ),
                    'text/html'
                );
            // on envoie le message
            $mailer->send($message);
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('success', 'le bulletin a bien été envoyé');
            //On redirige l'utilisateur sur la page index.html.twig (acceuil).
            return $this->redirectToRoute('home1');
        }
        //On envoie les données sur la page adhesion/index.html.twig (adhésion).
        return $this->render('adhesion/index.html.twig', [
            'adhesionForm' => $form->createView(),
        ]);

    }
}
