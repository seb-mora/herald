<?php

namespace App\Controller;

use DateTime;
use App\Utils\Sort;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/messages')]
class MessagesController extends AbstractController
{
    /**
     * Retourne le nombre de messages non ouverts par leurs destinataires
     * @param MessagesRepository $messagesRepository
     * @return Response
     */
    #[Route('/', name: 'admin_com_index', methods: ['GET'])]
    public function indexCom(MessagesRepository $messagesRepository): Response
    {
        $messagesNonLus = $messagesRepository->findBy(['lu' => 0]);

        return $this->render('admin/communication.html.twig', [
            'messagesNonLus' => count($messagesNonLus)
        ]);
    }

    /**
     * Affiche l'ensemble des messages envoyés
     * @param MessagesRepository $messagesRepository
     * @param Sort $sort
     * @return Response
     */
    #[Route('/index', name: 'admin_messages_index', methods: ['GET'])]
    public function indexMessages(MessagesRepository $messagesRepository, Sort $sort): Response
    {
        $messages = $messagesRepository->findAll();
        $messages = $sort->sortMsgDesc($messages);

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * Permet de créer et envoyer un nouveau message
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UsersRepository $usersRepository
     * @param $id
     * @return Response
     */
    #[Route('/new/{id}', name: 'admin_messages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository, $id = null): Response
    {
        $user = $usersRepository->findOneBy(['id' => $id]);
        $date = date('d-m-Y H:i:s');
        $format = 'd-m-Y H:i:s';
        $date = DateTime::createFromFormat($format, $date);
        $message = new Messages();

        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDate($date);
            $message->setLu(0);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('admin_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messages/new.html.twig', [
            'message' => $message,
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Permet de voir les détails d'un message
     * @param Messages $message
     * @return Response
     */
    #[Route('/{id}', name: 'admin_messages_show', methods: ['GET'])]
    public function show(Messages $message): Response
    {

        return $this->render('messages/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * Permet la modification d'un message (non implémenté)
     * @param Request $request
     * @param Messages $message
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_messages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messages/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    /**
     * Permet la suppression d'un message (non implémenté)
     * @param Request $request
     * @param Messages $message
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_messages_delete', methods: ['POST'])]
    public function delete(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_messages_index', [], Response::HTTP_SEE_OTHER);
    }
}
