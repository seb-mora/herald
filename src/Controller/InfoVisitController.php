<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\InfoVisit;
use App\Form\InfoVisitType;
use App\Form\InfoVisitEditType;
use App\Repository\InfoVisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/info/visit')]
class InfoVisitController extends AbstractController
{
    #[Route('/', name: 'admin_info_visit_index', methods: ['GET'])]
    public function index(InfoVisitRepository $infoVisitRepository, Sort $sort): Response
    {
        $infoVisits = $infoVisitRepository->findAll();
        $infoVisits = $sort->sortInfoDesc($infoVisits);
        $infoVisitsToShow = [];
        $infoVisitsShow = [];
        $infoVisitsToHide = [];
        $dt = new \DateTime();
        foreach ($infoVisits as $info) {
            if ($info->getDateHide() < $dt && $info->getDateHide() !== null)
                $infoVisitsToHide[] = $info;
            elseif ($info->getDateShow() <= $dt && ($info->getDateHide() >= $dt || $info->getDateHide() == null))
                $infoVisitsShow[] = $info;
            elseif ($info->getDateShow() > $dt)
                $infoVisitsToShow[] = $info;
        }

        return $this->render('info_visit/index.html.twig', [
            'infoVisitsToShow' => $infoVisitsToShow,
            'infoVisitsShow' => $infoVisitsShow,
            'infoVisitsToHide' => $infoVisitsToHide
        ]);
    }


    #[Route('/new', name: 'admin_info_visit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infoVisit = new InfoVisit();
        $form = $this->createForm(InfoVisitType::class, $infoVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateShow = $form->getData()->getDateShow();
            $dateHide = $form->getData()->getDateHide();
            if ($dateHide != null && $dateShow >= $dateHide) {
                return $this->renderForm('info_visit/new.html.twig', [
                    'info_visit' => $infoVisit,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($infoVisit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_info_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_visit/new.html.twig', [
            'info_visit' => $infoVisit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_info_visit_show', methods: ['GET'])]
    public function show(InfoVisit $infoVisit): Response
    {
        return $this->render('info_visit/show.html.twig', [
            'info_visit' => $infoVisit,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_info_visit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoVisit $infoVisit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfoVisitEditType::class, $infoVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateShow = $form->getData()->getDateShow();
            $dateHide = $form->getData()->getDateHide();
            if ($dateHide != null && $dateShow >= $dateHide) {
                return $this->renderForm('info_visit/edit.html.twig', [
                    'info_user' => $infoVisit,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($infoVisit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_info_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_visit/edit.html.twig', [
            'info_user' => $infoVisit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_visit_delete', methods: ['POST'])]
    public function delete(Request $request, InfoVisit $infoVisit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $infoVisit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infoVisit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_info_visit_index', [], Response::HTTP_SEE_OTHER);
    }
}
