<?php


namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PartenaireController extends AbstractController
{

    /**
     * @Route ("/partenaire", name="partenaire")
     * @param PartenaireRepository $partenaireRepository
     * @return Response
     *
     * Cette méthode sert a rediriger sur la page Gestion des partenaires,
     * et a afficher tous les partenaires présent en base donnée.
     *
     */
    public function partenaire(PartenaireRepository $partenaireRepository): Response
    {
        //On récupère toutes les données de la table activité avec la méthode findPartenaire().
        return $this->render('partenaire/partenaire.html.twig',[
        //On envoie les données récupérer sur la page partenaire.html.twig.
            'partenaires' => $partenaireRepository->findPartenaire(),
        ]);
    }

    /**
     * @Route ("/createP", name="createP")
     * @param Request $request
     * @return Response
     *
     * Cette méthode sert a créer un partenaire.
     *
     */
    public function createPartenaire(Request $request): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //On créer une nouvelle instance de l'objet Partenaire et on le stock dans la variable $partenaire.
        $partenaire = new Partenaire();
        //On créer notre formulaire.
        $formPartenaire = $this->createForm(PartenaireType::class, $partenaire);
        //On récupère les information saisi.
        $formPartenaire->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if($formPartenaire->isSubmitted() && $formPartenaire->isValid())
        {
            //On stock le nom du fichier dans la variable $file.
            $file = $partenaire->getLogo();
            //On renomme le fichier avec un nom unique et on lui ajoute l'extension contenue
            //dans la variable $file, on stock le tout dans la variable $fileName.
            $fileName = md5(uniqid()). '.' .$file->guessExtension();
            //On déplace le fichier dans le repository logos
            $file->move($this->getParameter('logo_directory'),$fileName);
            //On injecte le nouveau nom du fichier dans la propriété image.
            $partenaire->setLogo($fileName);
            //On envoie les informations a la base de donnée
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('success', 'Votre image a bien été insérer dans l\'album');
            //On redirige l'utilisateur sur la page partenaire.html.twig
            return $this->redirectToRoute('partenaire');
        }
        //On envoie les données sur la page _formPartenaire.html.twig
        return $this->render('partenaire/_formPartenaire.html.twig',[
            'partenaire' => $partenaire,
            'formPartenaire' => $formPartenaire->createView(),
        ]);

    }

    /**
     * @Route ("/update/{id}", name="partenaire_update")
     * @param Partenaire $partenaire
     * @param Request $request
     * @return RedirectResponse|Response
     *
     * Cette méthode sert a modifier un partenaire.
     *
     */
    public function partenaireUpdate(Partenaire $partenaire, Request $request): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        //On récupère le nom de l'image stocké en base donnée et le stock dans la variable $nom
        $nom = $partenaire->getLogo();
        //On créer notre formulaire.
        $form = $this->createForm(PartenaireType::class, $partenaire);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {
            //Si la variable $nom est diffèrente de null(rien)...
            if($nom != null) {
                //On supprime le fichier stocker dans le repository logos.
                unlink($this->getParameter('logo_directory') . '/' . $nom);
            }
            //On stock le nom du fichier dans la variable $file.
            $file = $partenaire->getLogo();
            //On renomme le fichier avec un nom unique et on lui ajoute l'extension contenue
            //dans la variable $file, on stock le tout dans la variable $fileName.
            $fileName = md5(uniqid()). '.' .$file->guessExtension();
            //On déplace le fichier dans le repository logos.
            $file->move($this->getParameter('logo_directory'),$fileName);
            //On injecte le nouveau nom du fichier dans la propriété image.
            $partenaire->setLogo($fileName);
            //On envoie les informations a la base de donnée.
            $entityManager->persist($partenaire);
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('success', 'La documentation à été modifié avec succès ');
            //On redirige l'utilisateur sur la page partenaire.html.twig.
            return $this->redirectToRoute('partenaire');
        }
        //On envoie les données sur la page updatePartenaire.html.twig
        return $this->render('partenaire/updatePartenaire.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="partenaire_delete")
     * @param Partenaire $partenaire
     * @return RedirectResponse
     *
     * Cette méthode sert a supprimer un partenaire.
     *
     */
    public function partenaireDelete(Partenaire $partenaire): RedirectResponse
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_USER");

        $em = $this->getDoctrine()->getManager();
        //On récupère le nom de l'image stocké en base de donnée.
        $nom = $partenaire->getLogo();
        //On supprime le fichier stocker dans le repository logos.
        unlink($this->getParameter('logo_directory') . '/' . $nom);
        //On supprime le fichier stocker dans la base de donnée
        $em->remove($partenaire);
        $em->flush();
        //On redirige l'utilisateur sur la page partenaire.html.twig.
        return $this->redirectToRoute('partenaire');
    }


}