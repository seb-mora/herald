<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Equipes;
use App\Entity\Status;
use App\Entity\Clients;
use App\Entity\Chantiers;
use App\Entity\Users;
use App\Entity\InfoUser;
use App\Entity\Messages;
use App\Entity\InfoVisit;
use App\Entity\Photos;
use App\Entity\EquipChantier;
use App\Repository\ChantiersRepository;
use App\Repository\EquipesRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $faker;
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->equipesFixtures($manager);
        $this->statusFixtures($manager);
        $this->clientsFixtures($manager);
        $this->infoUserFixtures($manager);
        $this->infoVisitFixtures($manager);
        $this->chantiersFixtures($manager);
        $this->usersFixtures($manager);
        $this->messagesFixtures($manager);
        $this->photosFixtures($manager);
        $this->equipChantierFixtures($manager);
    }

    protected function equipesFixtures($manager)
    {
        $equipe = new Equipes;
        $equipe->setNom("direction");

        $manager->persist($equipe);
        $this->addReference('direction', $equipe);

        for ($i = 1; $i <= 3; $i++) {
            $equipe = new Equipes;
            $equipe->setNom('equipe' . $i);

            $manager->persist($equipe);
            $this->addReference('equipe' . $i, $equipe);
        }
        $manager->flush();
    }

    protected function statusFixtures($manager)
    {
        $status = new Status;
        $status->setNom("admin");
        $status->setCoutSemaine(0);
        $manager->persist($status);
        $this->addReference('admin', $status);

        for ($i = 1; $i <= 3; $i++) {
            $status = new Status;
            $status->setNom("status" . $i);
            $status->setCoutSemaine(mt_rand(0, 20));

            $manager->persist($status);
            $this->addReference('status' . $i, $status);
        }
        $manager->flush();
    }

    protected function clientsFixtures($manager)
    {
        $client = new Clients;
        $client->setNom("Herald");
        $client->setAdresse($this->faker->sentence(6));
        $client->setTelephone(mt_rand(99999, 999999));

        $manager->persist($client);
        $this->addReference('Herald', $client);

        for ($i = 1; $i <= 6; $i++) {
            $client = new Clients;
            $client->setNom("client" . $i);
            $client->setAdresse($this->faker->country());
            $client->setTelephone(mt_rand(99999, 999999));

            $manager->persist($client);
            $this->addReference('client' . $i, $client);
        }
        $manager->flush();
    }

    protected function chantiersFixtures($manager)
    {
        $chantier = new Chantiers;
        $chantier->setNom("Bureau");
        $client = $this->getReference('Herald');
        $chantier->setFkClient($client);
        $chantier->setLocalisation("au dépôt");
        $chantier->setDateDebut(null);
        $chantier->setDateFin(null);
        $chantier->setDureeSem(1);
        $chantier->setMontant(mt_rand(99999, 999999999));
        $chantier->setFactureEmise(0);
        $chantier->setPaiementRecu(0);
        $chantier->setRetourEquipe($this->faker->sentence(5));
        $chantier->setRetourClient($this->faker->sentence(8));
        $chantier->setClos(0);

        $manager->persist($chantier);
        $this->addReference('bureau', $chantier);

        for ($i = 1; $i <= 5; $i++) {
            $chantier = new Chantiers;
            $chantier->setNom("chantier" . $i);
            $client = $this->getReference('client' . $i);
            $chantier->setFkClient($client);
            $chantier->setLocalisation($this->faker->country());
            $chantier->setDateDebut(null);
            $chantier->setDateFin(null);
            $chantier->setDureeSem($i);
            $chantier->setMontant(mt_rand(99999, 999999999));
            $chantier->setFactureEmise(0);
            $chantier->setPaiementRecu(0);
            $chantier->setRetourEquipe($this->faker->sentence(5));
            $chantier->setRetourClient($this->faker->sentence(8));
            $chantier->setClos(0);
            $chantier->setDescription($this->faker->sentence(8));

            $manager->persist($chantier);
            $this->addReference('chantier' . $i, $chantier);
        }
        $manager->flush();
    }

    protected function usersFixtures($manager)
    {

        $employe = new Users;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employe,
            'password'
        );
        $employe->setNom($this->faker->lastName());
        $employe->setPrenom($this->faker->firstName());
        $employe->setAdresse($this->faker->sentence(4));
        $employe->setTelephone(mt_rand(99999, 999999));
        $equipe = $this->getReference('direction');
        $employe->setFkEquipe($equipe);
        $status = $this->getReference('admin');
        $employe->setFkStatus($status);
        $employe->setRoles(['ROLE_ADMIN']);
        $employe->setDateIn($this->faker->dateTime('d_m_Y'));
        $employe->setPresent(1);
        $employe->setDateOut($this->faker->dateTime('d_m_Y'));
        $employe->setLogin("admin");
        $employe->setPassword($hashedPassword);

        $manager->persist($employe);
        $this->addReference('administrateur', $employe);

        for ($i = 1; $i <= 29; $i++) {
            $employe = new Users;
            $hashedPassword = $this->passwordHasher->hashPassword(
                $employe,
                'password'
            );
            $employe->setNom($this->faker->lastName());
            $employe->setPrenom($this->faker->firstName());
            $employe->setAdresse('adresse employé ' . $i);
            $employe->setTelephone(mt_rand(99999, 999999));
            $equipe = $this->getReference('equipe' . mt_rand(1, 3));
            $employe->setFkEquipe($equipe);
            $status = $this->getReference('status' . mt_rand(1, 3));
            $employe->setFkStatus($status);
            $employe->setRoles(['ROLE_REGISTERED']);
            $employe->setDateIn($this->faker->datetime('d_m_Y'));
            $employe->setPresent(1);
            $employe->setDateOut($this->faker->dateTime('d_m_Y'));
            $employe->setLogin("employé N°" . $i);
            $employe->setPassword($hashedPassword);

            $manager->persist($employe);
            $this->addReference('employe' . $i, $employe);
        }
        $manager->flush();
    }

    protected function infoUserFixtures($manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $infoUser = new InfoUser;
            $infoUser->setTitre($this->faker->word());
            $infoUser->setContenu($this->faker->paragraph());
            $infoUser->setDateShow($this->faker->dateTime('d_m_Y'));
            $infoUser->setDateHide(null);

            $manager->persist($infoUser);
        }
        $manager->flush();
    }

    protected function infoVisitFixtures($manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $infoVisit = new InfoVisit;
            $infoVisit->setTitre($this->faker->word());
            $infoVisit->setContenu($this->faker->paragraph());
            $infoVisit->setDateShow($this->faker->dateTime('d_m_Y'));
            $infoVisit->setDateHide(null);

            $manager->persist($infoVisit);
        }
        $manager->flush();
    }

    protected function messagesFixtures($manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $message = new Messages;
            $employe = $this->getReference('employe' . $i);
            $message->setFkDestinataire($employe);
            $message->setSujet($this->faker->word());
            $message->setContenu($this->faker->paragraph());
            $message->setDate($this->faker->dateTime('d_m_Y'));
            $message->setLu(0);

            $manager->persist($message);
        }
        $manager->flush();
    }

    protected function photosFixtures($manager)
    {
        for ($i = 1; $i <= 4; $i++) {
            $photo = new Photos;
            $chantier = $this->getReference('chantier' . (mt_rand(1, 5)));
            $photo->setFkChantier($chantier);
            $photo->setLien('https://loremflickr.com/640/480/lanscape');
            $photo->setDescription($this->faker->paragraph());
            $photo->setPrincipale(0);

            $manager->persist($photo);
        }
        $manager->flush();
    }

    protected function equipChantierFixtures($manager)
    {
        $equipChan1 = new EquipChantier;
        $equipChan1->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan1->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan1->setDateIn(null);
        $equipChan1->setDateOut(null);
        $manager->persist($equipChan1);

        $equipChan2 = new EquipChantier;
        $equipChan2->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan2->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan2->setDateIn(null);
        $equipChan2->setDateOut(null);
        $manager->persist($equipChan2);

        $equipChan3 = new EquipChantier;
        $equipChan3->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan3->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan3->setDateIn(null);
        $equipChan3->setDateOut(null);
        $manager->persist($equipChan3);

        $equipChan4 = new EquipChantier;
        $equipChan4->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan4->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan4->setDateIn(null);
        $equipChan4->setDateOut(null);
        $manager->persist($equipChan4);

        $equipChan5 = new EquipChantier;
        $equipChan5->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan5->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan5->setDateIn(null);
        $equipChan5->setDateOut(null);
        $manager->persist($equipChan5);

        $equipChan6 = new EquipChantier;
        $equipChan6->setFkEquipe($this->getRandomReference('App\Entity\Equipes', $manager));
        $equipChan6->setFkChantier($this->getRandomReference('App\Entity\Chantiers', $manager));
        $equipChan6->setDateIn(null);
        $equipChan6->setDateOut(null);
        $manager->persist($equipChan6);

        $manager->flush();
    }

    protected function getReferencedObject(string $className, int $id, object $manager)
    {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager)
    {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }
}
