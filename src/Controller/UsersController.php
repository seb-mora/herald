<?php

namespace App\Controller;

use App\Utils\Sort;
use App\Entity\Users;
use App\Form\UsersType;
use App\Entity\Chantiers;
use App\Repository\UsersRepository;
use App\Repository\PhotosRepository;
use App\Repository\StatusRepository;
use App\Repository\EquipesRepository;
use App\Repository\InfoUserRepository;
use App\Repository\MessagesRepository;
use App\Repository\ChantiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipChantierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/users')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'app_users_index', methods: ['GET'])]
    public function index(
        UsersRepository $usersRepository,
        MessagesRepository $messagesRepository,
        EquipesRepository $equipesRepository,
        InfoUserRepository $infoUserRepository,
        Sort $sort
    ): Response {
        $user = $this->getUser();
        $userStatus = $user->getFkStatus();
        if ($userStatus->getNom() == "admin") {
            return $this->render('home/userIndex.html.twig', [
                'users' => $equipesRepository->findAll(),
            ]);
        }

        $infoUsers = $infoUserRepository->findAll();
        $infoUsers = $sort->sortInfoDesc($infoUsers);
        $dt = new \DateTime();
        $InfoShowable = [];
        foreach ($infoUsers as $infoUser) {
            if ($infoUser->getDateShow() <= $dt && ($infoUser->getDateHide() >= $dt || $infoUser->getDateHide() === null))
                $InfoShowable[] = $infoUser;
        }

        $nonLus = $messagesRepository->findBy(['fk_destinataire' => $user->getId(), 'lu' => 0]);


        return $this->render('home/userIndex.html.twig', [
            // 'users' => $usersRepository->findAll(),
            'infoUser' => $InfoShowable,
            'nonLus' => $nonLus
        ]);
    }


    #[Route('/equipe/{id}', name: 'show_equipe', methods: ['GET'])]
    public function showEquipe(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, Users $user, $id): Response
    {
        $user = $this->getUser();

        if ($user->getId() != $id) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }

        $userEquipe = $user->getFkEquipe();
        $membres = $userEquipe->getUsers();
        $equipe = $userEquipe->getNom();

        return $this->render('userView/equipe.html.twig', [
            'equipe' => $membres,
            'nom' => $equipe,
        ]);
    }


    #[Route('/chantiers/{id}', name: 'list_chantiers', methods: ['GET'])]
    public function listChantiers(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, PhotosRepository $photosRepository, Users $user, Sort $sort, $id): Response
    {
        $user = $this->getUser();

        if ($user->getId() != $id) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }

        $photos = [];
        $photosPrinc = $photosRepository->findBy(['principale' => true]);
        foreach ($photosPrinc as $photo) {
            $photos[$photo->getFkChantier()->getId()] = $photo->getLien();
        }

        $chantiers = [];
        $userEquipe = $user->getFkEquipe();
        $equipChantiers = $userEquipe->getChantiers();

        foreach ($equipChantiers as $equipChant) {
            $chantier = $equipChant->getFkChantier();
            $chantiers[] = $chantier;
        }
        $chantiers = $sort->sortChtDesc($chantiers);

        return $this->render('userView/chantiers.html.twig', [
            'photos' => $photos,
            'chantiers' => $chantiers,
        ]);
    }

    #[Route('/chantier/{id}', name: 'show_chantier', methods: ['GET'])]
    public function showChantier(Chantiers $chantier,  PhotosRepository $photosRepository): Response
    {
        if ($this->getUser()) {

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

            return $this->render('userView/viewChantier.html.twig', [
                'chantier' => $chantier,
                'photoPrinc' => $photoPrinc,
                'photoSec' => $photoSec,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/equipes/autres/{id}', name: 'autres_equipes', methods: ['GET'])]
    public function showAutresEquipes(Users $user, EquipesRepository $equipesRepository, ChantiersRepository $chantiersRepository, StatusRepository $statusRepository, Sort $sort): Response
    {
        $AutresEquipes = [];
        $user = $this->getUser();
        $userEquipe = $user->getFkEquipe();
        $equipes = $equipesRepository->findAll();
        $equipes = $sort->sortEmpAsc($equipes);


        foreach ($equipes as $equipeIndex => $equipe) {
            if ($userEquipe->getId() === $equipe->getId() || $equipe->getNom() == 'direction') {
                unset($equipes[$equipeIndex]);
            }
        }

        foreach ($equipes as $equipeIndex => $equipe) {
            $dt = new \DateTime();
            $tab = [];
            $tab['chantier'] = 'aucun';
            $tab['localisation'] = '';
            $tab['responsable'] = 'aucun';
            $tab['telephone'] = '';

            $idResp = $statusRepository->findOneBy(['nom' => 'Responsable'])->getId();;
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

        return $this->render('userView/autresEquipes.html.twig', [
            'equipes' => $AutresEquipes,
        ]);
    }

    #[Route('/messages/{id}', name: 'show_messages', methods: ['GET'])]
    public function showMessages(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, MessagesRepository $messagesRepository, Sort $sort, $id): Response
    {

        $user = $this->getUser();

        if ($user->getId() != $id) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }

        $messages = $messagesRepository->findBy(['fk_destinataire' => $user->getId()]);
        $messages = $sort->sortMsgDesc($messages);
        $messagesLus = [];
        $messagesNonLus = [];
        foreach ($messages as $message) {
            if ($message->isLu() == 1)
                $messagesLus[] = $message;
            else
                $messagesNonLus[] = $message;
        }

        return $this->render('userView/messages.html.twig', [
            'messagesLus' => $messagesLus,
            'messagesNonLus' => $messagesNonLus,
            'unread' => count($messagesNonLus),
        ]);
    }

    #[Route('/messages/read/{id}', name: 'read_message', methods: ['GET'])]
    public function readMessage(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, MessagesRepository $messagesRepository, EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $user = $this->getUser();
        $message = $messagesRepository->findOneBy(['id' => $id]);


        if ($user->getId() != $message->getFkDestinataire()->getId()) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }
        $message->setLu(true);
        $entityManagerInterface->persist($message);
        $entityManagerInterface->flush();

        return $this->render('userView/viewMessage.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/planning/{id}', name: 'user_planning', methods: ['GET'])]
    public function showPlanning(Request $request, EquipChantierRepository $equipChantierRepository, $id): Response
    {
        $dt = new \DateTime();
        if ($request->query->has('selected_date')) {
            $dt = $request->query->get('selected_date');
        }

        $user = $this->getUser();
        $equipeId = $user->getFkEquipe()->getId();
        $chantiers = $equipChantierRepository->findBy(['fk_equipe' => $equipeId]);

        return $this->render('userView/planning.html.twig', [
            'chantiers' => $chantiers,
            'laDate' => $dt,
        ]);
    }

    #[Route('/unread/{id}', name: 'unread', methods: ['GET'])]
    public function unread(UsersRepository $usersRepository, InfoUserRepository $infoUserRepository, MessagesRepository $messagesRepository, $id): Response
    {

        $user = $this->getUser();

        if ($user->getId() != $id) {
            return $this->render('home/userIndex.html.twig', [
                'users' => $usersRepository->findAll(),
                'infoUser' => $infoUserRepository->findAll(),
            ]);
        }

        $nbrNonLus = count($messagesRepository->findBy(['fk_destinataire' => $id, 'lu' => 0]));
        return $this->render('userView/_userSideMenu.html.twig', [
            'nbrNonLus' => $nbrNonLus,
        ]);
    }
}
