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
    #[Route('/', name: 'admin_com_index', methods: ['GET'])]
    public function indexCom(MessagesRepository $messagesRepository): Response
    {
        $messagesNonLus = $messagesRepository->findBy(['lu' => 0]);

        return $this->render('admin/communication.html.twig', [
            'messagesNonLus' => count($messagesNonLus)
        ]);
    }


    #[Route('/index', name: 'admin_messages_index', methods: ['GET'])]
    public function indexMessages(MessagesRepository $messagesRepository, Sort $sort): Response
    {
        $messages = $messagesRepository->findAll();
        $messages = $sort->sortMsgDesc($messages);

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }

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

    #[Route('/{id}', name: 'admin_messages_show', methods: ['GET'])]
    public function show(Messages $message): Response
    {

        return $this->render('messages/show.html.twig', [
            'message' => $message,
        ]);
    }

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
