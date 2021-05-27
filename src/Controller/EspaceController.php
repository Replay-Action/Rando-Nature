<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Entity\User;
use App\Form\ActiviteType;
use App\Form\DocPdfType;
use App\Form\SearchForm;
use App\Repository\ActiviteRepository;
use App\Repository\DocPdfRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
class EspaceController extends AbstractController
{
    /**
     * @Route("/trombi", name="trombi")
     */
    public function index(UserRepository $userRepository, PhotoRepository $photoRepository): Response
    {
        #ce controlleur ne gère que le trombinoscope

        $this->denyAccessUnlessGranted("ROLE_USER");


        return $this->render('espace/index.html.twig', [
            'controller_name' => 'EspaceController',
            'users' => $userRepository->findUsers(),
            // 'users2'=>$userRepository->findUsers2(),
            //  'photos1'=>$photoRepository->findAll(),
        ]);
    }


    /**
     * @Route("/recapo", name="recap", methods={"GET"})
     */
    public function recap(ActiviteRepository $activiteRepository, Request $request): Response{
        $user = $this->getUser();
        $this->denyAccessUnlessGranted("ROLE_USER");
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);
        $products= $activiteRepository->findSearch($data);
        $activite=$activiteRepository->affichefinie();


        return $this->render('espace/recap-activite.html.twig',[
            'user'=>$user,
            'activites'=>$activite,
            'form'=>$form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/editrecapo", name="editrecap", methods={"GET","POST"})
     */
    public function editrecap(Request $request, Activite $activite, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->createForm(ActiviteType::class, $activite);

        #gere le traitement du formulaire#
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $pdf=$form->get('docPdfs')->getData();

            // on boucle sur les pdf
            //   foreach ($pdf as $pdf1) {
// on genere un nouveau nom de fichier
            if ($pdf) {

                $fichier = md5(uniqid()) . '.' . $pdf->guessExtension();
                $pdf->move(
                    $this->getParameter('upload_recap_directory'),
                    $fichier
                );

                // on stocke son nom en bdd
                $upload = new DocPdf();
                $upload->setNompdf($fichier);
                $activite->addDocPdf($upload);

                // }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activite);
                $entityManager->flush();

            }


            return $this->redirectToRoute('home1');

        }
        return $this->render('espace/edit-recap.html.twig',[
            'form' => $form->createView(),
            'activite'=>$activite]);


    }




    /**
     * @Route("/removerecap/pdf/{id}", name="removerecap", methods={"DELETE"})
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
            dd($data);
        }
        return $this->redirectToRoute('home1');
    }


    /**
     * @Route("/detailrecap/{id}", name="detailrecap", methods={"GET"})
     */
    public function voir (Activite $activite): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        return $this->render('espace/voir-recap.html.twig', [
            'activite' => $activite,
        ]);
    }


}
