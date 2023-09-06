<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\InfoUser;
use App\Form\InfoUserType;
use App\Form\InfoUserEditType;
use App\Repository\InfoUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/info/user')]
class InfoUserController extends AbstractController
{
    #[Route('/', name: 'admin_info_user_index', methods: ['GET'])]
    public function index(InfoUserRepository $infoUserRepository, Sort $sort): Response
    {
        $infoUsers = $infoUserRepository->findAll();
        $infoUsers = $sort->sortInfoDesc($infoUsers);
        $infoUsersToShow = [];
        $infoUsersShow = [];
        $infoUsersToHide = [];
        $dt = new \DateTime();
        foreach ($infoUsers as $info) {
            if ($info->getDateHide() < $dt && $info->getDateHide() !== null)
                $infoUsersToHide[] = $info;
            elseif ($info->getDateShow() <= $dt && ($info->getDateHide() >= $dt || $info->getDateHide() == null))
                $infoUsersShow[] = $info;
            elseif ($info->getDateShow() > $dt)
                $infoUsersToShow[] = $info;
        }

        return $this->render('info_user/index.html.twig', [
            'infoUsersToShow' => $infoUsersToShow,
            'infoUsersShow' => $infoUsersShow,
            'infoUsersToHide' => $infoUsersToHide
        ]);
    }

    #[Route('/new', name: 'admin_info_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infoUser = new InfoUser();
        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateShow = $form->getData()->getDateShow();
            $dateHide = $form->getData()->getDateHide();
            if ($dateHide != null && $dateShow >= $dateHide) {
                return $this->renderForm('info_user/new.html.twig', [
                    'info_user' => $infoUser,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($infoUser);
            $entityManager->flush();

            return $this->redirectToRoute('admin_info_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_user/new.html.twig', [
            'info_user' => $infoUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_info_user_show', methods: ['GET'])]
    public function show(InfoUser $infoUser): Response
    {
        return $this->render('info_user/show.html.twig', [
            'info_user' => $infoUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_info_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoUser $infoUser, EntityManagerInterface $entityManager, InfoUserRepository $infoUserRepository, $id): Response
    {
        $form = $this->createForm(InfoUserEditType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateShow = $form->getData()->getDateShow();
            $dateHide = $form->getData()->getDateHide();
            if ($dateHide != null && $dateShow >= $dateHide) {
                return $this->renderForm('info_user/edit.html.twig', [
                    'info_user' => $infoUser,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($infoUser);
            $entityManager->flush();

            return $this->redirectToRoute('admin_info_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_user/edit.html.twig', [
            'info_user' => $infoUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_user_delete', methods: ['POST'])]
    public function delete(Request $request, InfoUser $infoUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $infoUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infoUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_info_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
