<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Form\ActiviteType;
use App\Form\SearchForm;
use App\Repository\ActiviteRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class EspaceController extends AbstractController
{
    /**
     * @Route("/trombi", name="trombi")
     * @param UserRepository $userRepository
     * @return Response
     *
     *
     * Cette methode est en charge d'afficher le trombinoscope
     *
     */
    public function index(UserRepository $userRepository): Response
    {
        //On as accès a cette page a partir du moment qu'on est Adhérent
        $this->denyAccessUnlessGranted("ROLE_USER");

        //On redirige l'utilisateur sur la page espace/index.html.twig.
        return $this->render('espace/index.html.twig', [
            'users' => $userRepository->orderUserByReferentWithPhoto(),
        ]);
    }


    /**
     * @Route("/recapo", name="recap", methods={"GET"})
     * @param ActiviteRepository $activiteRepository
     * @param Request $request
     * @return Response
     *
     *
     * Cette methode est en charge d'afficher la page Récap Sorties
     *
     */
    public function recap(ActiviteRepository $activiteRepository, Request $request): Response{
        $user = $this->getUser();
        //On laisse l'accès à cette fonction seulement aux Administrateur.
        $this->denyAccessUnlessGranted("ROLE_USER");

        $data = new SearchData();
        //On récupère le formulaire dans SearchForm.
        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);
        $activite=$activiteRepository->affichefinie();

        //On redirige l'utilisateur sur la page recap-activite.html.twig.
        return $this->render('espace/recap-activite.html.twig',[
            'user'=>$user,
            'activites'=>$activite,
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/editrecapo", name="editrecap", methods={"GET","POST"})
     * @param Request $request
     * @param Activite $activite
     * @return RedirectResponse|Response
     *
     *
     * Cette methode est en charge de modifier un pdf lié a une activite
     *
     */
    public function editrecap(Request $request, Activite $activite){
        //On laisse l'accès à cette fonction seulement aux Administrateur.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //On récupère le formulaire dans ActualiteType.
        $form = $this->createForm(ActiviteType::class, $activite);

        //Gere le traitement du formulaire
        $form->handleRequest($request);
        //Si le formulaire a été envoyer et est valide ...
        if ($form->isSubmitted() && $form->isValid()) {

            $pdf=$form->get('docPdfs')->getData();

            //On genere un nouveau nom de fichier
            if ($pdf) {

                $fichier = md5(uniqid()) . '.' . $pdf->guessExtension();
                $pdf->move(
                    $this->getParameter('upload_recap_directory'),
                    $fichier
                );

                //On envoie les informations à la base de donnée.
                $upload = new DocPdf();
                $upload->setNompdf($fichier);
                $activite->addDocPdf($upload);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activite);
                $entityManager->flush();

            }

            //On redirige l'utilisateur sur la page home/index.html.twig.
            return $this->redirectToRoute('home1');

        }
        //On envoie les données et l'affichage du formulaire sur la page edit-recap.html.twig.
        return $this->render('espace/edit-recap.html.twig',[
            'form' => $form->createView(),
            'activite'=>$activite]);


    }


    /**
     * @Route("/removerecap/pdf/{id}", name="removerecap", methods={"DELETE"})
     * @param DocPdf $docPdf
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     *
     *
     * Cette Methode est en charge de supprimer un pdf lié a une activite
     *
     */
    public function removerecap(DocPdf $docPdf, Request $request){

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $data = json_decode($request->getContent(), true);
        dump($data);
        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $docPdf->getId(), $data['_token'])) {
            // On récupère le nom du pdf
            $nom = $docPdf->getNompdf();


            // On supprime le fichier
            unlink($this->getParameter('upload_recap_directory') . '/' . $nom);


            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($docPdf);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
        return $this->redirectToRoute('home1');
    }


    /**
     * @Route("/detailrecap/{id}", name="detailrecap", methods={"GET"})
     * @param Activite $activite
     * @return Response
     */
    public function voir (Activite $activite): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        return $this->render('espace/voir-recap.html.twig', [
            'activite' => $activite,
        ]);
    }


}
