<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805143703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidature_formateur_matiere (candidature_formateur_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_B6F5E76D4EAA6B9B (candidature_formateur_id), INDEX IDX_B6F5E76DF46CD258 (matiere_id), PRIMARY KEY (candidature_formateur_id, matiere_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE candidature_formateur_matiere ADD CONSTRAINT FK_B6F5E76D4EAA6B9B FOREIGN KEY (candidature_formateur_id) REFERENCES candidature_formateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_formateur_matiere ADD CONSTRAINT FK_B6F5E76DF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_formateur DROP matieres');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature_formateur_matiere DROP FOREIGN KEY FK_B6F5E76D4EAA6B9B');
        $this->addSql('ALTER TABLE candidature_formateur_matiere DROP FOREIGN KEY FK_B6F5E76DF46CD258');
        $this->addSql('DROP TABLE candidature_formateur_matiere');
        $this->addSql('ALTER TABLE candidature_formateur ADD matieres LONGTEXT NOT NULL');
    }
}
