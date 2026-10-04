<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261004192225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE session_document (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, session_id INT NOT NULL, INDEX IDX_53C5EA1E613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE session_document ADD CONSTRAINT FK_53C5EA1E613FECDF FOREIGN KEY (session_id) REFERENCES session_revision (id)');

        // Nouvelle colonne nullable d'abord, pour pouvoir y recopier les
        // matières existantes (ex ManyToMany) avant de forcer NOT NULL —
        // sinon la ligne déjà en base perd ses matières silencieusement.
        $this->addSql('ALTER TABLE candidature_formateur ADD matieres LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE candidature_formateur cf SET matieres = (
            SELECT GROUP_CONCAT(m.nom SEPARATOR \', \')
            FROM candidature_formateur_matiere cfm
            JOIN matiere m ON m.id = cfm.matiere_id
            WHERE cfm.candidature_formateur_id = cf.id
        )');
        $this->addSql('UPDATE candidature_formateur SET matieres = \'\' WHERE matieres IS NULL');
        $this->addSql('ALTER TABLE candidature_formateur MODIFY matieres LONGTEXT NOT NULL');

        $this->addSql('ALTER TABLE candidature_formateur_matiere DROP FOREIGN KEY `FK_B6F5E76D4EAA6B9B`');
        $this->addSql('ALTER TABLE candidature_formateur_matiere DROP FOREIGN KEY `FK_B6F5E76DF46CD258`');
        $this->addSql('DROP TABLE candidature_formateur_matiere');
        $this->addSql('ALTER TABLE inscription_session ADD methode_paiement VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidature_formateur_matiere (candidature_formateur_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_B6F5E76DF46CD258 (matiere_id), INDEX IDX_B6F5E76D4EAA6B9B (candidature_formateur_id), PRIMARY KEY (candidature_formateur_id, matiere_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE candidature_formateur_matiere ADD CONSTRAINT `FK_B6F5E76D4EAA6B9B` FOREIGN KEY (candidature_formateur_id) REFERENCES candidature_formateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_formateur_matiere ADD CONSTRAINT `FK_B6F5E76DF46CD258` FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_document DROP FOREIGN KEY FK_53C5EA1E613FECDF');
        $this->addSql('DROP TABLE session_document');
        $this->addSql('ALTER TABLE candidature_formateur DROP matieres');
        $this->addSql('ALTER TABLE inscription_session DROP methode_paiement');
    }
}
