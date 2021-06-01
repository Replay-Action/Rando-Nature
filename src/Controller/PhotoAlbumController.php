<?php


namespace App\Controller;


use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Entity\PhotoAlbum;
use App\Form\DocPdfType;
use App\Form\PhotoAlbumType;
use App\Repository\ActiviteRepository;
use App\Repository\DocPdfRepository;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PhotoAlbumController extends AbstractController
{
    /**
     * @Route ("/album", name="album")
     * @param PhotoRepository $photoRepository
     * @param ActiviteRepository $activiteRepository
     * @param DocPdfRepository $docPdfRepository
     * @return Response
     */
    public function album(PhotoRepository $photoRepository
        , ActiviteRepository $activiteRepository
        , DocPdfRepository $docPdfRepository): Response
    {

        return $this->render('album/album_photo.html.twig',[
            'activite' => $activiteRepository->findActivites(),
            'photos' =>  $photoRepository->findAll(),
            'pdf' => $docPdfRepository->findAll(),
        ]);
    }

    /**
     * @Route ("/pdf/create/{id}", name="create_pdf")
     * @param Request $request
     * @param Activite $activite
     * @return Response
     */
    public function createPdf(Request $request, Activite $activite): Response
    {
        $pdf = new DocPdf();

        $form = $this->createForm(DocPdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $pdf->setPdfactivite($activite);
            if ($pdf->getNompdf() != null)
            {
                $file = $form->get('nompdf')->getData();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_recap_directory'),$fileName);
                $pdf->setNompdf($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pdf);
            $entityManager->flush();
            return $this->redirectToRoute('album');
        }
        return $this->render('album/new_pdf.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/album/create/{id}", name="create_album")
     * @param Request $request
     * @param Activite $activite
     * @return Response
     */
    public function createAlbum(Request $request, Activite $activite): Response
    {
        $album = new PhotoAlbum();

        $form = $this->createForm(PhotoAlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if( $album->getImage() != null )
            {
                $album->setActivite($activite);
                $file = $album->getImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('album_directory'),$fileName);
                $album->setImage($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($album);
            $entityManager->flush();
            $this->addFlash('success', "Votre image a bien été insérer dans l'album");
        }
        return $this->render('album/new_album.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/show/album/{id}", name="show_album")
     * @param Activite $activite
     * @param ActiviteRepository $activiteRepository
     * @return Response
     */
    public function showAlbum(Activite $activite, ActiviteRepository $activiteRepository): Response{

        $activite = $activiteRepository->findOneBy(['id'=> $activite]);

        return $this->render('album/show_album.html.twig',[
            'activite' => $activite,
        ]);
    }

    /**
     * @Route ("/delete/album/{id}", name="delete_album")
     * @param PhotoAlbum $photoAlbum
     * @return Response
     */
    public function deleteAlbum(PhotoAlbum $photoAlbum): Response{
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();
        if ($photoAlbum->getImage() != null)
        {
            $nom = $photoAlbum->getImage();
            unlink($this->getParameter('album_directory').'/'.$nom);
        }
        $entityManager->remove($photoAlbum);
        $entityManager->flush();

        return $this->redirectToRoute('album');
    }
}