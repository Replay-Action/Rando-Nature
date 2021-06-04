<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Form\SearchForm;
use App\Repository\ActiviteRepository;
use App\Repository\DocPdfRepository;
use App\Repository\EtatRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/activite")
 */
class ActiviteController extends AbstractController
{
    /**
     * @Route("/", name="activite_index", methods={"GET"})
     * @param ActiviteRepository $activiteRepository
     * @param Request $request
     * @return Response
     *
     * Cette méthode est en charge de rediriger l'utilisateur sur a page Programme,
     * d'afficher les activités avec un état 'ouvert' ou 'modifier' et d'afficher un filtre.
     *
     */
    public function index(ActiviteRepository $activiteRepository, Request $request): Response
    {
        //On récupère l'utilisateur en session et le stock dans la variable $user.
        $user = $this->getUser();
        //On créer une nouvelle instance de dateTime et on récupère la date et l'heure actuel.
        $date = new DateTime('now');
        //On créer une nouvelle instance de l'objet SearchData et la stock dans la variable $data.
        $data = new SearchData();
        //On créer notre formulaire.
        $form = $this->createForm(SearchForm::class, $data);
        //On récupère les information saisi.
        $form->handleRequest($request);

        //on liste toutes les activités comme le findall mais en une requête
        $products = $activiteRepository->findSearch($data);

        #on liste toutes les activités comme le findall mais en une requete
        /**$acti = $activiteRepository->findSearch();**/

        # on cherche les activités dont la date est dépassée et on change leur état en 'finie'
        $acti2 = $activiteRepository->miseajouretat();

        # on met à jour l'etat qd on va sur la page de liste des activités
        $acti2;

        //On envoie les données sur la page index.html (Programme).
        return $this->render('activite/index.html.twig', [
            'user' => $user,
            /**'activites' => $acti,*/
            'date' => $date,
            'activites'=> $products,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="activite_new", methods={"GET","POST"})
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     *
     * Cette méthode sert a créer une activité.
     *
     */
    public function new(Request $request, EtatRepository $etatRepository): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        //On récupère l'utilisateur en session et le stock dans la variable $user.
        $user = $this->getUser();
        //On créer une nouvelle instance d' Activite et la stock dans la variable $activite.
        $activite = new Activite();

        //On créer notre formulaire.
        $form = $this->createForm(ActiviteType::class, $activite);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //on hydrate l'activite avec l'organisateur.
            $activite->setOrganisateur($user);

            #pour indiquer automatiquement l'etat 'ouverte' lors de la creation de l'activité'
            $etat = $etatRepository->findOneBy(['libelle' => 'ouverte']);
            $activite->setEtat($etat);

            //On envoie les informations a la base de donnée.
            $entityManager->persist($activite);
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('success', 'Une nouvelle activité est créée');
            //On redirige l'utilisateur sur la page index.html.twig (Programme).
            return $this->redirectToRoute('activite_index');
        }

        //On envoie les données sur la page new.html.twig (Formulaire de création).
        return $this->render('activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form->createView(),
            'user' => $user


        ]);
    }

    /**
     * @Route("/{id}", name="activite_show", methods={"GET"})
     * @param Activite $activite
     * @return Response
     *
     *
     *
     */
    public function show(Activite $activite): Response
    {
        return $this->render('activite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="activite_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Activite $activite
     * @param EtatRepository $etatRepository
     * @return Response
     *
     * Cette méthode sert a modifié une activité
     *
     */
    public function edit(Request $request, Activite $activite, EtatRepository $etatRepository): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        //pour recuperer le user en session et on le stock dans la variable $user.
        $user = $this->getUser();
        //on hydrate l'activite avec l'organisateur.
        $activite->setOrganisateur($user);

        #on met l'etat de l'activité à 'modifiée' qd on modifie
        $etat = $etatRepository->findOneBy(['libelle' => 'modifiée']);
        $activite->setEtat($etat);
        //On créer notre formulaire.
        $form = $this->createForm(ActiviteType::class, $activite);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {
            //On supprime le fichier stocker dans la base de donnée
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'activité modifiée !!');
            //On redirige l'utilisateur sur la page album.html.twig.
            return $this->redirectToRoute('activite_index');
        }
        //On envoie les données sur la page edit.html.twig
        return $this->render('activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="activite_delete", methods={"DELETE"})
     * @param Request $request
     * @param Activite $activite
     * @param DocPdfRepository $docPdfRepository
     * @return Response
     *
     * Cette méthode sert a supprimer une activité
     *
     */
    public function delete(Request $request, Activite $activite, DocPdfRepository $docPdfRepository): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        if ($this->isCsrfTokenValid('delete' . $activite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $activite1=$activite->getId();
            $pdf=$docPdfRepository->findOneBy(['pdfactivite' => $activite1]);

            if ($pdf != null) {
                $nompdf = $pdf->getNompdf();
                $pdfexist = $this->getParameter('upload_recap_directory') . '/' . $nompdf;

                //si le pdf existe dans le dossier public alors on l'efface
                if ($pdfexist) {
                    unlink($pdfexist);
                }
            }

            //On supprime le fichier stocker dans la base de donnée
            $entityManager->remove($activite);
            $entityManager->flush();
        }
        //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
        $this->addFlash('success', 'activité effacée');
        //On redirige l'utilisateur sur la page index.html.twig (Accueil)
        return $this->redirectToRoute('activite_index');
    }

    /**
     * @Route("/{id}/sinscrire", name="activite_sinscrire", methods={"GET","POST"})
     * @param Activite $activite
     * @return Response
     *
     * Cette méthode sert a s'inscrire a une activité.
     *
     */

    public function sinscrire(Activite $activite): Response
    {

        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle User.
        $this->denyAccessUnlessGranted("ROLE_USER");
        //On récupère les informations de l'utilisateur stocké en session.
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //On ajoute l'utilisateur a la liste des inscrits.
        $activite->addUser($user);
        //On supprime le fichier stocker dans la base de donnée
        $entityManager->persist($activite);
        $entityManager->flush();
        //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
        $this->addFlash('success', 'Vous êtes bien inscrit a une activité');
        //On redirige l'utilisateur sur la page index.html.twig (Accueil).
        return $this->redirectToRoute('activite_index');
    }


    /**
     * @Route("/{id}/sedesister", name="activite_sedesister", methods={"GET","POST"})
     * @param Activite $activite
     * @return Response
     *
     * Cette méthode sert a se désister d'une acitivté.
     *
     */
    public function sedesister(Activite $activite): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle User.
        $this->denyAccessUnlessGranted("ROLE_USER");
        //On récupère les informations de l'utilisateur stocké en session.
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //On supprime l'utilisateur a la liste des inscrits.
        $activite->removeUser($user);
        //On supprime le fichier stocker dans la base de donnée
        $entityManager->persist($activite);
        $entityManager->flush();
        //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
        $this->addFlash('success', 'Vous êtes désinscrit d une activité');
        //On redirige l'utilisateur sur la page index.html.twig (Accueil).
        return $this->redirectToRoute('activite_index');
    }

    /**
     * @Route ("/{id}/annuler", name="activite_annuler",methods={"GET","POST"})
     * @param Activite $activite
     * @param EtatRepository $etatRepository
     * @return Response
     *
     * Cette activité sert a annulé une activité.
     *
     */
    public function annuler(Activite $activite, EtatRepository $etatRepository): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle User.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();

        //on met l'état en tant que annulée dans la bdd.
        $etat = $etatRepository->findOneBy(['libelle' => 'annulée']);
        //On change l'état de l'activité a annulée.
        $activite->setEtat($etat);
        //On supprime le fichier stocker dans la base de donnée
        $entityManager->persist($activite);
        $entityManager->flush();
        //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
        $this->addFlash('success', 'activité annulée');
        //On redirige l'utilisateur sur la page index.html.twig (Accueil).
        return $this->redirectToRoute('activite_index');
    }
}
