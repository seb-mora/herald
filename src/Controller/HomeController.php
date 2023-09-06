<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Repository\PhotosRepository;
use App\Repository\ChantiersRepository;
use App\Repository\InfoVisitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ChantiersRepository $chantiersRepository, InfoVisitRepository $infoVisitRepository, PhotosRepository $photosRepository, Sort $sort): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('app_users_index');
        }

        $photos = [];
        $photosPrinc = $photosRepository->findBy(['principale' => true]);
        foreach ($photosPrinc as $photo) {
            $photos[$photo->getFkChantier()->getId()] = $photo->getLien();
        }

        $infoVisits = $infoVisitRepository->findAll();
        $infoVisits = $sort->sortInfoDesc($infoVisits);
        $dt = new \DateTime();
        $InfoShowable = [];
        foreach ($infoVisits as $infoVisit) {
            if ($infoVisit->getDateShow() <= $dt && ($infoVisit->getDateHide() >= $dt || $infoVisit->getDateHide() === null))
                $InfoShowable[] = $infoVisit;
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'chantiers' => $chantiersRepository->findAll(),
            'infoVisit' => $InfoShowable,
            'photos' => $photos,

        ]);
    }

    #[Route('/chantier/{id}', name: 'app_chantier')]
    public function visitChantier(PhotosRepository $photosRepository, ChantiersRepository $chantiersRepository, $id): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('app_users_index');
        }

        $chantier = $chantiersRepository->findOneBy(['id' => $id]);


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

        return $this->render('home/visitChantier.html.twig', [
            'chantier' => $chantier,
            'photoPrinc' => $photoPrinc,
            'photoSec' => $photoSec,
        ]);
    }
}
