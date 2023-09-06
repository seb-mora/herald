<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230815114259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chantiers (id INT AUTO_INCREMENT NOT NULL, fk_client_id INT NOT NULL, nom VARCHAR(36) NOT NULL, localisation VARCHAR(255) NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, duree_sem INT NOT NULL, montant NUMERIC(11, 2) NOT NULL, facture_emise TINYINT(1) NOT NULL, paiement_recu TINYINT(1) NOT NULL, retour_client LONGTEXT DEFAULT NULL, retour_equipe LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, clos TINYINT(1) NOT NULL, INDEX IDX_4FB3F70578B2BEB1 (fk_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(36) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equip_chantier (id INT AUTO_INCREMENT NOT NULL, fk_equipe_id INT NOT NULL, fk_chantier_id INT NOT NULL, date_in DATE DEFAULT NULL, date_out DATE DEFAULT NULL, INDEX IDX_7438907BCDFCC19 (fk_equipe_id), INDEX IDX_7438907BB936A8A9 (fk_chantier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(36) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_user (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_show DATETIME NOT NULL, date_hide DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_visit (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_show DATETIME NOT NULL, date_hide DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, fk_destinataire_id INT NOT NULL, sujet VARCHAR(36) NOT NULL, contenu LONGTEXT NOT NULL, date DATETIME NOT NULL, lu TINYINT(1) NOT NULL, INDEX IDX_DB021E967D5B1B5B (fk_destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, fk_chantier_id INT NOT NULL, lien VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, principale TINYINT(1) NOT NULL, INDEX IDX_876E0D9B936A8A9 (fk_chantier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(36) NOT NULL, cout_semaine NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, fk_equipe_id INT DEFAULT NULL, fk_status_id INT DEFAULT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(36) NOT NULL, prenom VARCHAR(36) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone INT NOT NULL, date_in DATE NOT NULL, present TINYINT(1) NOT NULL, date_out DATE DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), INDEX IDX_1483A5E9CDFCC19 (fk_equipe_id), INDEX IDX_1483A5E9AAED72D (fk_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chantiers ADD CONSTRAINT FK_4FB3F70578B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE equip_chantier ADD CONSTRAINT FK_7438907BCDFCC19 FOREIGN KEY (fk_equipe_id) REFERENCES equipes (id)');
        $this->addSql('ALTER TABLE equip_chantier ADD CONSTRAINT FK_7438907BB936A8A9 FOREIGN KEY (fk_chantier_id) REFERENCES chantiers (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E967D5B1B5B FOREIGN KEY (fk_destinataire_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9B936A8A9 FOREIGN KEY (fk_chantier_id) REFERENCES chantiers (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CDFCC19 FOREIGN KEY (fk_equipe_id) REFERENCES equipes (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9AAED72D FOREIGN KEY (fk_status_id) REFERENCES status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chantiers DROP FOREIGN KEY FK_4FB3F70578B2BEB1');
        $this->addSql('ALTER TABLE equip_chantier DROP FOREIGN KEY FK_7438907BCDFCC19');
        $this->addSql('ALTER TABLE equip_chantier DROP FOREIGN KEY FK_7438907BB936A8A9');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E967D5B1B5B');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9B936A8A9');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CDFCC19');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9AAED72D');
        $this->addSql('DROP TABLE chantiers');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE equip_chantier');
        $this->addSql('DROP TABLE equipes');
        $this->addSql('DROP TABLE info_user');
        $this->addSql('DROP TABLE info_visit');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
