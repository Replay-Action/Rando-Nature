<?php

namespace App\Controller;

use App\Form\AdhesionType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class AdhesionController extends AbstractController
{
    /**
     * @Route("/adhesion", name="adhesion")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(AdhesionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $adhesion = $form->getData();
            // envoi du mail
            $message = (new\Swift_Message('Nouvel Adherent'))

                // on attribue l'expediteur
                ->setFrom($adhesion['email'])

                // on attribue le destinataire
                ->setTo('vrnb2020@velorandonaturebruz.fr')

                // on créée le message avec le twig
                ->setBody(
                    $this->renderView(
                        'emails/buletin_adhesion.html.twig', compact('adhesion')
                    ),
                    'text/html'
                );
            // on envoie le message
            $mailer->send($message);

            $this->addFlash('success', 'le buletin a bien été envoyé');
            return $this->redirectToRoute('home1');
        }
        return $this->render('adhesion/index.html.twig', [
            'adhesionForm' => $form->createView(),
        ]);

    }
}
