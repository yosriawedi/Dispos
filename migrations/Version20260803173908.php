<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260803173908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidature_formateur (id INT AUTO_INCREMENT NOT NULL, matieres LONGTEXT NOT NULL, stacks LONGTEXT DEFAULT NULL, experience LONGTEXT DEFAULT NULL, diplomes LONGTEXT DEFAULT NULL, cv_url VARCHAR(255) DEFAULT NULL, disponibilite_heures INT DEFAULT NULL, tarif_horaire DOUBLE PRECISION DEFAULT NULL, statut VARCHAR(20) DEFAULT \'soumise\' NOT NULL, note_admin LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, candidat_id INT NOT NULL, INDEX IDX_FAAE9AC28D0EB82 (candidat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE candidature_recrutement (id INT AUTO_INCREMENT NOT NULL, lettre_motivation LONGTEXT DEFAULT NULL, cv_url VARCHAR(255) DEFAULT NULL, statut VARCHAR(20) DEFAULT \'soumise\' NOT NULL, message_entreprise LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, candidat_id INT NOT NULL, offre_id INT NOT NULL, INDEX IDX_CA1F4A68D0EB82 (candidat_id), INDEX IDX_CA1F4A64CC8505A (offre_id), UNIQUE INDEX unique_candidature_recrutement (candidat_id, offre_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contribution_projet_interne (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, note_admin LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, etudiant_id INT NOT NULL, projet_interne_id INT NOT NULL, INDEX IDX_1CAE6BFFDDEAB1A3 (etudiant_id), INDEX IDX_1CAE6BFFDFC0770D (projet_interne_id), UNIQUE INDEX unique_contribution (etudiant_id, projet_interne_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE demande_encadrement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, sujet VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, stack_tech LONGTEXT DEFAULT NULL, etablissement VARCHAR(255) DEFAULT NULL, niveau_etude VARCHAR(100) DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, note_admin LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, etudiant_id INT NOT NULL, encadreur_id INT DEFAULT NULL, INDEX IDX_1E25147DDDEAB1A3 (etudiant_id), INDEX IDX_1E25147DA625A0FD (encadreur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE demande_reduction (id INT AUTO_INCREMENT NOT NULL, contexte_prestation_ciblee VARCHAR(255) DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, metadonnees JSON DEFAULT NULL, message_admin LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, etudiant_id INT NOT NULL, offre_competence_id INT NOT NULL, INDEX IDX_C4CBAEBDDDEAB1A3 (etudiant_id), INDEX IDX_C4CBAEBD8D9F181E (offre_competence_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscription_session (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(20) DEFAULT \'confirmee\' NOT NULL, note_etudiant LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, etudiant_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_F9952338DDEAB1A3 (etudiant_id), INDEX IDX_F9952338613FECDF (session_id), UNIQUE INDEX unique_inscription (etudiant_id, session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, filiere VARCHAR(100) DEFAULT NULL, niveau VARCHAR(50) DEFAULT NULL, icone VARCHAR(10) DEFAULT NULL, active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offre_competence (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, stack LONGTEXT DEFAULT NULL, heures_estimees INT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'soumise\' NOT NULL, note_admin LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, etudiant_id INT NOT NULL, INDEX IDX_B98A0F5ADDEAB1A3 (etudiant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offre_recrutement (id INT AUTO_INCREMENT NOT NULL, poste VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type_contrat VARCHAR(20) NOT NULL, competences LONGTEXT DEFAULT NULL, localisation VARCHAR(100) DEFAULT NULL, teletravail TINYINT DEFAULT 0 NOT NULL, salaire VARCHAR(100) DEFAULT NULL, date_expiration DATETIME DEFAULT NULL, statut VARCHAR(20) DEFAULT \'publiee\' NOT NULL, created_at DATETIME NOT NULL, entreprise_id INT NOT NULL, INDEX IDX_6EC2A939A4AEAFEA (entreprise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projet_interne_dispos (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, domaine VARCHAR(100) DEFAULT NULL, competences_requises LONGTEXT DEFAULT NULL, places_max INT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'ouvert\' NOT NULL, date_limite DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session_revision (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, places_max INT DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, statut VARCHAR(20) DEFAULT \'planifiee\' NOT NULL, created_at DATETIME NOT NULL, matiere_id INT NOT NULL, formateur_id INT DEFAULT NULL, INDEX IDX_E6CF75A4F46CD258 (matiere_id), INDEX IDX_E6CF75A4155D8F51 (formateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE candidature_formateur ADD CONSTRAINT FK_FAAE9AC28D0EB82 FOREIGN KEY (candidat_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE candidature_recrutement ADD CONSTRAINT FK_CA1F4A68D0EB82 FOREIGN KEY (candidat_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE candidature_recrutement ADD CONSTRAINT FK_CA1F4A64CC8505A FOREIGN KEY (offre_id) REFERENCES offre_recrutement (id)');
        $this->addSql('ALTER TABLE contribution_projet_interne ADD CONSTRAINT FK_1CAE6BFFDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contribution_projet_interne ADD CONSTRAINT FK_1CAE6BFFDFC0770D FOREIGN KEY (projet_interne_id) REFERENCES projet_interne_dispos (id)');
        $this->addSql('ALTER TABLE demande_encadrement ADD CONSTRAINT FK_1E25147DDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE demande_encadrement ADD CONSTRAINT FK_1E25147DA625A0FD FOREIGN KEY (encadreur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE demande_reduction ADD CONSTRAINT FK_C4CBAEBDDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE demande_reduction ADD CONSTRAINT FK_C4CBAEBD8D9F181E FOREIGN KEY (offre_competence_id) REFERENCES offre_competence (id)');
        $this->addSql('ALTER TABLE inscription_session ADD CONSTRAINT FK_F9952338DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE inscription_session ADD CONSTRAINT FK_F9952338613FECDF FOREIGN KEY (session_id) REFERENCES session_revision (id)');
        $this->addSql('ALTER TABLE offre_competence ADD CONSTRAINT FK_B98A0F5ADDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE offre_recrutement ADD CONSTRAINT FK_6EC2A939A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE session_revision ADD CONSTRAINT FK_E6CF75A4F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE session_revision ADD CONSTRAINT FK_E6CF75A4155D8F51 FOREIGN KEY (formateur_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature_formateur DROP FOREIGN KEY FK_FAAE9AC28D0EB82');
        $this->addSql('ALTER TABLE candidature_recrutement DROP FOREIGN KEY FK_CA1F4A68D0EB82');
        $this->addSql('ALTER TABLE candidature_recrutement DROP FOREIGN KEY FK_CA1F4A64CC8505A');
        $this->addSql('ALTER TABLE contribution_projet_interne DROP FOREIGN KEY FK_1CAE6BFFDDEAB1A3');
        $this->addSql('ALTER TABLE contribution_projet_interne DROP FOREIGN KEY FK_1CAE6BFFDFC0770D');
        $this->addSql('ALTER TABLE demande_encadrement DROP FOREIGN KEY FK_1E25147DDDEAB1A3');
        $this->addSql('ALTER TABLE demande_encadrement DROP FOREIGN KEY FK_1E25147DA625A0FD');
        $this->addSql('ALTER TABLE demande_reduction DROP FOREIGN KEY FK_C4CBAEBDDDEAB1A3');
        $this->addSql('ALTER TABLE demande_reduction DROP FOREIGN KEY FK_C4CBAEBD8D9F181E');
        $this->addSql('ALTER TABLE inscription_session DROP FOREIGN KEY FK_F9952338DDEAB1A3');
        $this->addSql('ALTER TABLE inscription_session DROP FOREIGN KEY FK_F9952338613FECDF');
        $this->addSql('ALTER TABLE offre_competence DROP FOREIGN KEY FK_B98A0F5ADDEAB1A3');
        $this->addSql('ALTER TABLE offre_recrutement DROP FOREIGN KEY FK_6EC2A939A4AEAFEA');
        $this->addSql('ALTER TABLE session_revision DROP FOREIGN KEY FK_E6CF75A4F46CD258');
        $this->addSql('ALTER TABLE session_revision DROP FOREIGN KEY FK_E6CF75A4155D8F51');
        $this->addSql('DROP TABLE candidature_formateur');
        $this->addSql('DROP TABLE candidature_recrutement');
        $this->addSql('DROP TABLE contribution_projet_interne');
        $this->addSql('DROP TABLE demande_encadrement');
        $this->addSql('DROP TABLE demande_reduction');
        $this->addSql('DROP TABLE inscription_session');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE offre_competence');
        $this->addSql('DROP TABLE offre_recrutement');
        $this->addSql('DROP TABLE projet_interne_dispos');
        $this->addSql('DROP TABLE session_revision');
    }
}
