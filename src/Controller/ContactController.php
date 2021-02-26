<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // ici nous enverrons le mail
            $message = (new \Swift_Message('Nouveau Contact'))
                ->setFrom($contact['email'])

                // on attribue le destinataire - ci-dessous c'est le mail du site
                ->setTo('vrnb2020@velorandonaturebruz.fr')

                // on créée le message avec la vue twig (qui est dans les templates emails)
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ), 'text/html'
                );

            // on envoie le message
            $mailer->send($message);

            $this->addFlash('message', 'le message a bien été envoyé');
            return $this->redirectToRoute('home1');
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }


}
