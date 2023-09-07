<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\Users;
use App\Form\UsersType;
use App\Entity\Chantiers;
use App\Form\UsersEditType;
use App\Entity\EquipChantier;
use App\Repository\UsersRepository;
use App\Repository\EquipesRepository;
use App\Repository\InfoUserRepository;
use App\Repository\ChantiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipChantierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// TODO 
// virer les codes commentés
// mot de passe edit user
// user toujours connecté même apres déco
// blocage acces si pas admin et redirection
// préparer les "no records found" sur vues vides
// caler heures à création infoUser et infoVisit

//TODO PLUS TARD
// stocker messages effacés
// ajouter en bdd qui créé quoi
// effacer images des chantiers ?
// garder historique des équipes et chantiers en cas suppression/modification équipe ?
// changer mdp ?

#[Route('/admin')]
class AdminController extends AbstractController
{


    /**
     * Redirige vers la page communication si User connecté n'est pas admin
     * @param EquipesRepository $equipesRepository
     * @return Response
     */
    #[Route('/', name: 'admin_index', methods: ['GET'])]
    public function index(EquipesRepository $equipesRepository): Response
    {
        $user = $this->getUser();
        $userStatus = $user->getFkStatus();
        if ($userStatus->getNom() == "admin") {
            return $this->render('home/userIndex.html.twig', [
                'users' => $equipesRepository->findAll(),
            ]);
        }
        return $this->redirectToRoute('admin_com_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * Créé un nouvel User en base de données.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @return Response
     */
    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brutPassword = $form->getData()->getPassword();
            $hashedPassword = $userPasswordHasherInterface->hashPassword(
                $user,
                $brutPassword
            );
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('employes', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    // #[Route('/{id}', name: 'app_users_show', methods: ['GET'])]
    // public function show(Users $user): Response
    // {
    //     return $this->render('users/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }


    /**
     * Editer un User
     * @param Request $request
     * @param Users $user
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @return Response
     */
    #[Route('/edit/{id}', name: 'admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->createForm(UsersEditType::class, $user);
        $form->handleRequest($request);
        $oldPwd = $user->getPassword();

        if ($form->isSubmitted() && $form->isValid()) {
            $brutPassword = $form->getData()->getPassword();

            if (!is_null($brutPassword) && is_string($brutPassword)) {
                $hashedPassword = $userPasswordHasherInterface->hashPassword(
                    $user,
                    $brutPassword
                );
                $user->setPassword($hashedPassword);
            } else {
                $user->setPassword($oldPwd);
            }
            $entityManager->flush();

            return $this->redirectToRoute('employes', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'employe' => $user,
            'form' => $form,
        ]);
    }


    /**
     * Supprimer un User
     * @param Request $request
     * @param Users $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/delete/{id}', name: 'admin_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('employes', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }


    /**
     * Afficher la liste des Users par ordre alphabétique
     * @param UsersRepository $usersRepository
     * @param InfoUserRepository $infoUserRepository
     * @param Users $user
     * @param Sort $sort
     * @param $id
     * @return Response
     */
    #[Route('/employes/{id}', name: 'employes', methods: ['GET'])]
    public function employesList(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, Users $user, Sort $sort, $id): Response
    {
        $user = $this->getUser();

        if (!in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }
        $employes = $usersRepository->findAll();
        $employes = $sort->sortEmpAsc($employes);

        return $this->render('admin/employes.html.twig', [
            'employes' => $employes,
        ]);
    }


    /**
     * Voir la fiche d'un User
     * @param UsersRepository $usersRepository
     * @param InfoUserRepository $infoUserRepository
     * @param Users $user
     * @param $id
     * @return Response
     */
    #[Route('/employe/{id}', name: 'show_employe', methods: ['GET'])]
    public function showEmploye(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, Users $user, $id): Response
    {
        $user = $this->getUser();

        if (!in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }
        $employe = $usersRepository->findOneBy(['id' => $id]);

        return $this->render('admin/showEmploye.html.twig', [
            'employe' => $employe,
        ]);
    }


    /**
     * Affiche le planning commun et les chantier non attribués.
     * @param Request $request
     * @param Users $user
     * @param ChantiersRepository $chantiersRepository
     * @param EquipChantierRepository $equipChantierRepository
     * @param EquipesRepository $equipesRepository
     * @param $id
     * @return Response
     */
    #[Route('/planning/{id}', name: 'index_planning', methods: ['GET'])]
    public function indexPlanning(Request $request, Users $user, ChantiersRepository $chantiersRepository, EquipChantierRepository $equipChantierRepository, EquipesRepository $equipesRepository, $id): Response
    {
        $dt = new \DateTime();
        if ($request->query->has('selected_date')) {
            $dt = $request->query->get('selected_date');
        }

        $user = $this->getUser();

        if (!in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }

        // création de tableaux déjà préparés pour les envoyer à la vue.
        $equipes = $equipesRepository->findAll();
        $chantiers = $chantiersRepository->findAll();
        $equipChantiers = $equipChantierRepository->findAll();
        $chantiersNonOrga = [];
        $IdChantiersOrga = [];

        foreach ($equipes as $index => $equipe) {
            if ($equipe->getNom() === "direction")
                unset($equipes[$index]);
        }

        foreach ($equipChantiers as $equipChantier) {
            $IdChantiersOrga[] = $equipChantier->getFkChantier()->getId();
        }

        foreach ($chantiers as $index => $chantier) {
            if ($chantier->getNom() == "Bureau")
                unset($chantiers[$index]);
            else if (!in_array($chantier->getId(), $IdChantiersOrga))
                $chantiersNonOrga[] = $chantier;
        }

        return $this->render('admin/planning.html.twig', [
            'chantiers' => $chantiers,
            'equipes' => $equipes,
            'equipChantiers' => $equipChantiers,
            'chantiersNonOrga' => $chantiersNonOrga,
            'laDate' => $dt

        ]);
    }


    /**
     * Permet l'attribution d'un chantier à une équipe.
     * @param Request $request
     * @param Chantiers $chantier
     * @param EntityManagerInterface $entityManager
     * @param EquipesRepository $equipesRepository
     * @param ChantiersRepository $chantiersRepository
     * @param $id
     * @return Response
     */
    #[Route('/attribuer/{id}', name: 'admin_attrib', methods: ['POST'])]
    public function attribChantier(Request $request, Chantiers $chantier, EntityManagerInterface $entityManager, EquipesRepository $equipesRepository, ChantiersRepository $chantiersRepository, $id): Response
    {
        $equipeId = $request->request->get('equipe_assignment');

        $chantier = $chantiersRepository->findOneBy(['id' => $id]);
        $equipe = $equipesRepository->findOneBy(['id' => $equipeId]);
        $equipChantier = new EquipChantier();
        $equipChantier->setFkEquipe($equipe);
        $equipChantier->setFkChantier($chantier);
        $equipChantier->setDateIn($chantier->getDateDebut());
        $equipChantier->setDateOut($chantier->getDateFin());

        $entityManager->persist($equipChantier);
        $entityManager->flush();

        return $this->redirectToRoute('index_planning', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
