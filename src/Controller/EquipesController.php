<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\Users;
use App\Entity\Equipes;
use App\Form\EquipesType;
use App\Repository\UsersRepository;
use App\Repository\StatusRepository;
use App\Repository\EquipesRepository;
use App\Repository\ChantiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipChantierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/equipes')]
class EquipesController extends AbstractController
{
    /**
     * Permet l'affichage de toutes les équipes en présentant le nom de leur resposable, le chantier où elles sont actives actuellement et sa localisation
     * @param EquipesRepository $equipesRepository
     * @param ChantiersRepository $chantiersRepository
     * @param StatusRepository $statusRepository
     * @param Sort $sort
     * @return Response
     */
    #[Route('/', name: 'admin_equipes_index', methods: ['GET'])]
    public function Equipes(EquipesRepository $equipesRepository, ChantiersRepository $chantiersRepository, StatusRepository $statusRepository, Sort $sort): Response
    {
        $AutresEquipes = [];
        $equipes = $equipesRepository->findAll();
        $equipes = $sort->sortEmpAsc($equipes);

        foreach ($equipes as $equipeIndex => $equipe) {
            if ($equipe->getNom() == 'direction') {
                unset($equipes[$equipeIndex]);
            }
        }

        foreach ($equipes as $equipeIndex => $equipe) {
            $dt = new \DateTime();
            $tab = [];
            $tab['chantier'] = null;
            $tab['localisation'] = null;
            $tab['responsable'] = null;
            $tab['telephone'] = null;

            $idResp = $statusRepository->findOneBy(['nom' => 'Responsable'])->getId();;
            $tab['id'] = $equipe->getId();
            $tab['nom'] = $equipe->getNom();
            $equiChans = $equipe->getChantiers();
            foreach ($equiChans as $equiChan) {
                if ($equiChan->getDateIn() < $dt && $equiChan->getDateOut() != null || $equiChan->getDateOut() > $dt) {
                    $idChan = $equiChan->getFkChantier();
                    $chan = $chantiersRepository->findOneBy(['id' => $idChan]);
                    $tab['chantier'] = $chan->getNom();
                    $tab['localisation'] = $chan->getLocalisation();
                }
            }

            $equiUsers = $equipe->getUsers();
            foreach ($equiUsers as $equiUser) {

                if ($equiUser->getFkStatus()->getId() == $idResp) {
                    $tab['responsable'] = $equiUser->getPrenom() . " " . $equiUser->getNom();
                    $tab['telephone'] = $equiUser->getTelephone();
                }
            }
            $AutresEquipes[] = $tab;
        }

        return $this->render('equipes/index.html.twig', [
            'equipes' => $AutresEquipes,
        ]);
    }

    /**
     * Permet de créer une nouvelle équipe
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'admin_equipes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipes();
        $form = $this->createForm(EquipesType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('admin_equipes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipes/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    /**
     * Permet de voir l'organigramme d'une équipe et d'en modifier les membres.
     * @param EquipChantierRepository $equipChantierRepository
     * @param UsersRepository $usersRepository
     * @param Equipes $equipe
     * @param $id
     * @return Response
     */
    #[Route('/{id}', name: 'admin_equipes_show', methods: ['GET'])]
    public function show(EquipChantierRepository $equipChantierRepository, UsersRepository $usersRepository, Equipes $equipe, $id): Response
    {
        $chantiers = $equipChantierRepository->findBy(['fk_equipe' => $equipe]);
        $membres = $equipe->getUsers();
        $membresLibres = $usersRepository->findBy(['fk_equipe' => null]);

        return $this->render('equipes/show.html.twig', [
            'membres' => $membres,
            'equipe' => $equipe,
            'membresLibres' => $membresLibres,
            'chantiers' => $chantiers
        ]);
    }

    /**
     * Permet de changer le nom d'une équipe
     * @param Request $request
     * @param Equipes $equipe
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/edit/{id}', name: 'admin_equipes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipes $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipesType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_equipes_show', ['id' => $equipe->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipes/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    /**
     * Permet d'inclure un employé dans une équipe
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param EquipesRepository $equipesRepository
     * @param UsersRepository $usersRepository
     * @param $idEquipe
     * @param $idEmploye
     * @return Response
     */
    #[Route('/set/{idEquipe}/{idEmploye}', name: 'admin_equipes_set', methods: ['GET', 'POST'])]
    public function setEmploye(Request $request,  EntityManagerInterface $entityManager, EquipesRepository $equipesRepository, UsersRepository $usersRepository, $idEquipe, $idEmploye): Response
    {
        $equipe = $equipesRepository->findOneBy(['id' => $idEquipe]);
        $employe = $usersRepository->findOneBy(['id' => $idEmploye]);
        $set = $employe->setFkEquipe($equipe);
        $entityManager->persist($set);
        $entityManager->flush();

        return $this->redirectToRoute('admin_equipes_show', ['id' => $equipe->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de retirer un user d'une équipe
     * @param Request $request
     * @param Users $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/unset/{id}', name: 'admin_equipes_unset', methods: ['GET', 'POST'])]
    public function unsetEmploye(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $equipe = $user->getFkEquipe();
        $unset = $user->setFkEquipe(null);
        $entityManager->persist($unset);
        $entityManager->flush();

        return $this->redirectToRoute('admin_equipes_show', ['id' => $equipe->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de supprimer une équipe.
     * @param Request $request
     * @param Equipes $equipe
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'admin_equipes_delete', methods: ['POST'])]
    public function delete(Request $request, Equipes $equipe, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $equipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_equipes_index', [], Response::HTTP_SEE_OTHER);
    }
}
