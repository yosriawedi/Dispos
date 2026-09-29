<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805150417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_consultation (id INT AUTO_INCREMENT NOT NULL, date_demande DATETIME NOT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, message LONGTEXT NOT NULL, reponse_admin LONGTEXT DEFAULT NULL, incubation_id INT NOT NULL, INDEX IDX_C18F17DC9EB448E4 (incubation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE etape_incubation (id INT AUTO_INCREMENT NOT NULL, phase VARCHAR(30) NOT NULL, statut VARCHAR(25) DEFAULT \'non_demarree\' NOT NULL, commentaire_admin LONGTEXT DEFAULT NULL, date_mise_ajour DATETIME NOT NULL, incubation_id INT NOT NULL, referent_id INT DEFAULT NULL, INDEX IDX_802FA58D9EB448E4 (incubation_id), INDEX IDX_802FA58D35E47E35 (referent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE incubation (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, statut_global VARCHAR(20) DEFAULT \'en_cours\' NOT NULL, secteur_activite VARCHAR(100) NOT NULL, stade_maturite VARCHAR(20) NOT NULL, entreprise_id INT NOT NULL, INDEX IDX_B0B749D1A4AEAFEA (entreprise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE demande_consultation ADD CONSTRAINT FK_C18F17DC9EB448E4 FOREIGN KEY (incubation_id) REFERENCES incubation (id)');
        $this->addSql('ALTER TABLE etape_incubation ADD CONSTRAINT FK_802FA58D9EB448E4 FOREIGN KEY (incubation_id) REFERENCES incubation (id)');
        $this->addSql('ALTER TABLE etape_incubation ADD CONSTRAINT FK_802FA58D35E47E35 FOREIGN KEY (referent_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE incubation ADD CONSTRAINT FK_B0B749D1A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_consultation DROP FOREIGN KEY FK_C18F17DC9EB448E4');
        $this->addSql('ALTER TABLE etape_incubation DROP FOREIGN KEY FK_802FA58D9EB448E4');
        $this->addSql('ALTER TABLE etape_incubation DROP FOREIGN KEY FK_802FA58D35E47E35');
        $this->addSql('ALTER TABLE incubation DROP FOREIGN KEY FK_B0B749D1A4AEAFEA');
        $this->addSql('DROP TABLE demande_consultation');
        $this->addSql('DROP TABLE etape_incubation');
        $this->addSql('DROP TABLE incubation');
    }
}
