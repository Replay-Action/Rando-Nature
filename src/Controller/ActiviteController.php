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
     */
    public function index(ActiviteRepository $activiteRepository, Request $request): Response
    {

        $user = $this->getUser();
        $date = new DateTime('now');


        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);
        $products = $activiteRepository->findSearch($data);

        #on liste toutes les activités comme le findall mais en une requete
        /**$acti = $activiteRepository->findSearch();**/

        # on cherche les activités dont la date est dépassée et on change leur état en 'finie'
        $acti2 = $activiteRepository->miseajouretat();

        # on met à jour l'etat qd on va sur la page de liste des activités
        $acti2;


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
     */
    public function new(Request $request, EtatRepository $etatRepository): Response
    {

        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        #pour recuperer le user quand on cree une sortie
        $user = $this->getUser();


        $activite = new Activite();

        #on hydrate l'activite avec l'organisateur
        $activite->setOrganisateur($user);


        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            #pour indiquer automatiquement l'etat 'ouverte' lors de la creation de l'activité'
            $etat = $etatRepository->findOneBy(['libelle' => 'ouverte']);
            $activite->setEtat($etat);


            $entityManager->persist($activite);

            $entityManager->flush();
            $this->addFlash('success', 'Une nouvelle activité est créée');

            return $this->redirectToRoute('activite_index');
        }


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
     */
    public function edit(Request $request, Activite $activite, EtatRepository $etatRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        #pour recuperer le user quand on cree une sortie
        $user = $this->getUser();
        #on hydrate l'activite avec l'organisateur
        $activite->setOrganisateur($user);



        #on met l'etat de l'activité à 'modifiée' qd on modifie
        $etat = $etatRepository->findOneBy(['libelle' => 'modifiée']);
        $activite->setEtat($etat);

        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'activité modifiée !!');

            return $this->redirectToRoute('activite_index');
        }

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
     */
    public function delete(Request $request, Activite $activite, DocPdfRepository $docPdfRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        if ($this->isCsrfTokenValid('delete' . $activite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $activite1=$activite->getId();
            $pdf=$docPdfRepository->findOneBy(['pdfactivite' => $activite1]);

            if ($pdf != null) {
                $nompdf = $pdf->getNompdf();
                $pdfexist = $this->getParameter('upload_recap_directory') . '/' . $nompdf;

                #si le pdf existe dans le dossier public alors on l'efface
                if ($pdfexist) {
                    unlink($pdfexist);
                }
            }


            $entityManager->remove($activite);
            $entityManager->flush();
        }
        $this->addFlash('success', 'activité effacée');

        return $this->redirectToRoute('activite_index');
    }

    /**
     * @Route("/{id}/sinscrire", name="activite_sinscrire", methods={"GET","POST"})
     * @param Activite $activite
     * @return Response
     */

    public function sinscrire(Activite $activite): Response
    {
        #fonction s'inscrire dans le tableau des activites

        $this->denyAccessUnlessGranted("ROLE_USER");

        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $activite->addUser($user);

        $entityManager->persist($activite);
        $entityManager->flush();
        $this->addFlash('success', 'Vous êtes bien inscrit a une activité');

        return $this->redirectToRoute('activite_index');
    }


    /**
     * @Route("/{id}/sedesister", name="activite_sedesister", methods={"GET","POST"})
     * @param Activite $activite
     * @return Response
     */

    public function sedesister(Activite $activite): Response
    {
        #fonction se désister dans le tableau des activites

        $this->denyAccessUnlessGranted("ROLE_USER");
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $activite->removeUser($user);

        $entityManager->persist($activite);
        $entityManager->flush();
        $this->addFlash('success', 'Vous êtes désinscrit d une activité');

        return $this->redirectToRoute('activite_index');
    }

    /**
     * @Route ("/{id}/annuler", name="activite_annuler",methods={"GET","POST"})
     * @param Activite $activite
     * @param EtatRepository $etatRepository
     * @return Response
     */

    public function annuler(Activite $activite, EtatRepository $etatRepository): Response
    {
        #fonction annuler une activité dans le tableau des activites

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();

        #on met l'état en tant que annulée dans la bdd
        $etat = $etatRepository->findOneBy(['libelle' => 'annulée']);
        $activite->setEtat($etat);

        $entityManager->persist($activite);
        $entityManager->flush();
        $this->addFlash('success', 'activité annulée');

        return $this->redirectToRoute('activite_index');
    }
}
