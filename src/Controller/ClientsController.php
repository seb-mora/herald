<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\Clients;
use App\Form\ClientsType;
use App\Repository\ClientsRepository;
use App\Repository\ChantiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/clients')]
class ClientsController extends AbstractController
{
    /**
     * Affiche la liste des clients
     * @param ClientsRepository $clientsRepository
     * @param Sort $sort
     * @return Response
     */
    #[Route('/', name: 'admin_clients_index', methods: ['GET'])]
    public function index(ClientsRepository $clientsRepository, Sort $sort): Response
    {
        return $this->render('clients/index.html.twig', [
            'clients' => $sort->sortEmpAsc($clientsRepository->findAll()),
        ]);
    }

    /**
     * Permet la création d'un nouveau client
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'admin_clients_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Clients();
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('admin_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * Permet de voir la fiche client
     * @param Clients $client
     * @param ChantiersRepository $chantiersRepository
     * @param Sort $sort
     * @return Response
     */
    #[Route('/{id}', name: 'admin_clients_show', methods: ['GET'])]
    public function show(Clients $client, ChantiersRepository $chantiersRepository, Sort $sort): Response
    {
        $chantiers = $chantiersRepository->findBy(['fk_client' => $client->getId()]);
        $chantiers = $sort->sortChtDesc($chantiers);

        return $this->render('clients/show.html.twig', [
            'client' => $client,
            'chantiers' => $chantiers
        ]);
    }

    /**
     * Permet d'éditer un client
     * @param Request $request
     * @param Clients $client
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'admin_clients_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * Permet de supprimer un client (non implémenté)
     * @param Request $request
     * @param Clients $client
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_clients_delete', methods: ['POST'])]
    public function delete(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_clients_index', [], Response::HTTP_SEE_OTHER);
    }
}
