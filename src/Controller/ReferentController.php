<?php


namespace App\Controller;


use App\Entity\Referent;
use App\Form\ReferentType;
use App\Repository\ReferentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReferentController extends AbstractController
{

    /**
     * @Route ("/referent", name="referent")
     * @param ReferentRepository $referentRepository
     * @return Response
     */
    public function referent(ReferentRepository $referentRepository):Response
    {
        $products = $referentRepository->findAll();
        return $this->render('referent/referent.html.twig', [
             'referents' => $products
        ]);
    }

    /**
     * @Route ("/createRef", name="createRef")
     * @param Request $request
     * @return Response
     */
    public function createReferent(Request $request): Response
    {
        #autorise l'accès uniquement aux administrateurs du site#
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $referent = new Referent();
    $form = $this->createForm(ReferentType::class,$referent);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($referent);
        $entityManager->flush();
        $this->addFlash('succes', 'Un nouveau référent à été crée');
        return $this->redirectToRoute('referent');

    }

        return $this->render('referent/newref.html.twig',[
            'referent' => $referent,
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route ("/updateRef/{id}", name="updateRef")
     * @param Request $request
     * @param Referent $referent
     * @return Response
     */
    public function updateReferent(Request $request, Referent $referent):Response
    {
        #fonction qui permet de modifier un référent#

        #autorise l'accès uniquement aux administrateurs du site#
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ReferentType::class, $referent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form-> isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('succes', 'Le référent à été modifier');
            return $this->redirectToRoute('referent');
        }
        return $this->render('referent/editref.html.twig', [
            'referent' => $referent,
            'form' =>$form->createView(),
        ]);
    }

    /**
     * @Route ("/deleteRef/{id}", name="deleteRef")
     * @param Referent $referent
     * @return Response
     */
    public function deleteReferent(Referent $referent): Response
    {
         #fonction qui supprime un référent#

        #autorise l'accès uniquement aux administrateurs du site#
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em=$this->getDoctrine()->getManager();
        $em->remove($referent);
        $em->flush();
        $this->addFlash('succes', 'Le référent à été supprimer');
        return $this->redirectToRoute('referent');
    }
}