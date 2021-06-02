<?php


namespace App\Controller;


use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Entity\PhotoAlbum;
use App\Form\DocPdfType;
use App\Form\PhotoAlbumType;
use App\Repository\ActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PhotoAlbumController extends AbstractController
{
    /**
     * @Route ("/album", name="album")
     * @param ActiviteRepository $activiteRepository
     * @return Response
     */
    public function album(ActiviteRepository $activiteRepository): Response
    {

        return $this->render('album/album_photo.html.twig',[
            'activite' => $activiteRepository->findActivites(),
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
            //On lie l'Album a son Activité.
            //On lui injecte donc l'activité.
            $album->setActivite($activite);
            //Si getImage n'est pas null (vide),
            //on récupère le nom du fichier envoyé dans le formulaire,
            //on lui créer un identifiant (nom de fichier) unique
            // et on le place dans le répertoire qu'on aura choisi.
            //On donne le nouveau nom de fichier a l'attribut image.
            //avant de le stocker en base de donnée
            if( $album->getImage() != null )
            {
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
        //Si getImage n'est pas null (vide),
        //on récupère le nom de l'image stocké en base de donnée
        //et on supprime le fichier stocker dans le projet puis dans la DB
        if ($photoAlbum->getImage() != null)
        {
            $nom = $photoAlbum->getImage();
            unlink($this->getParameter('album_directory').'/'.$nom);
        }
        $entityManager->remove($photoAlbum);
        $entityManager->flush();

        return $this->redirectToRoute('album');
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
     * @Route ("/delete/pdf/{id}", name="delete_pdf")
     * @param DocPdf $docPdf
     * @return Response
     */
    public function deletePdf(DocPdf $docPdf):Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();
        //Si getImage n'est pas null (vide),
        //on récupère le nom de l'image stocké en base de donnée
        //et on supprime le fichier stocker dans le projet puis dans la DB
        if ($docPdf->getNompdf() != null)
        {
            $nom = $docPdf->getNompdf();
            unlink($this->getParameter('upload_recap_directory').'/'.$nom);
        }
        $entityManager->remove($docPdf);
        $entityManager->flush();

        return $this->redirectToRoute('album');
    }
}