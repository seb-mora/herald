<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\Photos;
use App\Entity\Chantiers;
use App\Form\ChantiersType;
use App\Repository\PhotosRepository;
use App\Repository\ChantiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipChantierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/chantiers')]
class ChantiersController extends AbstractController
{
    /**
     * Affiche la liste des chantiers
     * @param ChantiersRepository $chantiersRepository
     * @param PhotosRepository $photosRepository
     * @param Sort $sort
     * @return Response
     */
    #[Route('/', name: 'admin_chantiers_index', methods: ['GET'])]
    public function index(ChantiersRepository $chantiersRepository, PhotosRepository $photosRepository, Sort $sort): Response
    {
        $photos = [];
        $photosPrinc = $photosRepository->findBy(['principale' => true]);
        foreach ($photosPrinc as $photo) {
            $photos[$photo->getFkChantier()->getId()] = $photo->getLien();
        }

        return $this->render('chantiers/index.html.twig', [
            'photos' => $photos,
            'chantiers' => $sort->sortChtDesc($chantiersRepository->findAll()),
        ]);
    }

    /**
     * Permet la crétion d'un chantier
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_chantiers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chantier = new Chantiers();
        $form = $this->createForm(ChantiersType::class, $chantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($chantier);
            $entityManager->flush();

            if ($form->get('photo')->getData()) {
                $photo = new Photos();
                $photo->setLien($form->get('photo')->getData());
                $photo->setDescription($form->get('descrphotos')->getData());
                $photo->setFkChantier($chantier);
                if ($form->get('principale')->getData()) {
                    $photo->setPrincipale(true);
                } else {
                    $photo->setPrincipale(false);
                }
                $entityManager->persist($photo);
                $entityManager->flush();
            }

            return $this->redirectToRoute('admin_chantiers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chantiers/new.html.twig', [
            'chantier' => $chantier,
            'form' => $form,
        ]);
    }

    /**
     * Permet de voir la fiche chantier
     * @param Chantiers $chantier
     * @param PhotosRepository $photosRepository
     * @param EquipChantierRepository $equipChantierRepository
     * @return Response
     */
    #[Route('/{id}', name: 'admin_chantiers_show', methods: ['GET'])]
    public function show(Chantiers $chantier, PhotosRepository $photosRepository, EquipChantierRepository $equipChantierRepository): Response
    {
        $equipesChantier = $equipChantierRepository->findBy(['fk_chantier' => $chantier]);
        $allPhotos = $photosRepository->findBy(['fk_chantier' => $chantier]);
        $photoPrinc = null;
        $photoSec = [];

        foreach ($allPhotos as $photo) {
            if ($photo->isPrincipale()) {
                $photoPrinc = $photo;
            } else {
                $photoSec[] = $photo;
            }
        }

        return $this->render('chantiers/show.html.twig', [
            'chantier' => $chantier,
            'photoPrinc' => $photoPrinc,
            'photoSec' => $photoSec,
            'equipesChantier' => $equipesChantier,
        ]);
    }

    /**
     * Permet de modifier un chantier
     * @param Request $request
     * @param Chantiers $chantier
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_chantiers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chantiers $chantier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChantiersType::class, $chantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newphotolink = $form->get('photo')->getData();
            $descr = $form->get('descrphotos')->getData();
            $princ = $form->get('principale')->getData();

            if ($newphotolink !== null) {
                $photo = new Photos();
                $photo->setFkChantier($chantier);
                $photo->setLien($newphotolink);
                $photo->setDescription($descr);
                $photo->setPrincipale($princ);
                $entityManager->persist($photo);
            }
            $entityManager->flush();

            return $this->redirectToRoute('admin_chantiers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chantiers/edit.html.twig', [
            'chantier' => $chantier,
            'form' => $form,
        ]);
    }

    /**
     * Permet de supprimer un chantier (non implémenté).
     * @param Request $request
     * @param Chantiers $chantier
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_chantiers_delete', methods: ['POST'])]
    public function delete(Request $request, Chantiers $chantier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chantier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chantier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_chantiers_index', [], Response::HTTP_SEE_OTHER);
    }
}
