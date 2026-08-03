<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260803162457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(20) DEFAULT \'open\' NOT NULL, needed_skills LONGTEXT DEFAULT NULL, team_size INT DEFAULT NULL, created_at DATETIME NOT NULL, startup_id INT NOT NULL, INDEX IDX_2FB3D0EE67B339C5 (startup_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE startup (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, sector VARCHAR(100) NOT NULL, logo VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, stage VARCHAR(20) DEFAULT \'seed\' NOT NULL, founded_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_E48E50F67E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE startup_tag (startup_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_83345A1667B339C5 (startup_id), INDEX IDX_83345A16BAD26311 (tag_id), PRIMARY KEY (startup_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, color VARCHAR(7) DEFAULT NULL, UNIQUE INDEX UNIQ_389B783989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, locale VARCHAR(10) DEFAULT \'fr\' NOT NULL, avatar VARCHAR(255) DEFAULT NULL, bio VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE67B339C5 FOREIGN KEY (startup_id) REFERENCES startup (id)');
        $this->addSql('ALTER TABLE startup ADD CONSTRAINT FK_E48E50F67E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE startup_tag ADD CONSTRAINT FK_83345A1667B339C5 FOREIGN KEY (startup_id) REFERENCES startup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE startup_tag ADD CONSTRAINT FK_83345A16BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE67B339C5');
        $this->addSql('ALTER TABLE startup DROP FOREIGN KEY FK_E48E50F67E3C61F9');
        $this->addSql('ALTER TABLE startup_tag DROP FOREIGN KEY FK_83345A1667B339C5');
        $this->addSql('ALTER TABLE startup_tag DROP FOREIGN KEY FK_83345A16BAD26311');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE startup');
        $this->addSql('DROP TABLE startup_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
    }
}
