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

/**
 * Affiche le formulaire de modification d'un produit existant.
 * GET /product/update/{idProduct}
 * @param array $assocParams Tableau associatif des paramètres.
 * @return void
 * @throws Exception Si le tableau associatif contient l'une des clés 'view' ou 'assocParams'.
 */

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
     * Affiche la page Communication à l'accueil, redirige sur l'index User si l'User logué n'est pas Admin.
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
     * Affiche le formulaire de création d'un User et le traite une fois soumis.
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

    /**
     * Affiche les détails d'un User.
     * GET /admin/{id}
     * @param Class Users
     * @return Response
     */
    // #[Route('/{id}', name: 'app_users_show', methods: ['GET'])]
    // public function show(Users $user): Response
    // {
    //     return $this->render('users/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    /**
     * Affiche le formulaire d'édition d'un user et le traite une fois soumis.
     * GET /admin/edit/{id}
     * @param Class Users
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
     * Permet la suppression d'un User de la base de données.
     * POST /admin/delete/{id}
     * @param Class Users
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
     * Affiche la liste des employés
     * GET /admin/employes/{id}
     * @param Class Users
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
     * Affiche les détails d'un employé
     * GET /admin/employe/{id}
     * @param Class Users
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
     * Affiche le planning général
     * GET /admin/planning/{id}
     * @param Class Users
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
     * Attribue le chantier à une équipe en créant une nouvelle entrée dans la table equip_chantier
     * POST /admin/attribuer/{id}
     * @param Class Chantiers
     * @param Class Equipes
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
