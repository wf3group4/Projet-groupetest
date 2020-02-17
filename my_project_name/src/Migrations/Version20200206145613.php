<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<< HEAD:my_project_name/src/Migrations/Version20200217080842.php
final class Version20200217080842 extends AbstractMigration
=======
final class Version20200206145613 extends AbstractMigration
>>>>>>> origin/sandra:my_project_name/src/Migrations/Version20200206145613.php
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

<<<<<<< HEAD:my_project_name/src/Migrations/Version20200217080842.php
        $this->addSql('ALTER TABLE annonces ADD closed_at DATETIME DEFAULT NULL');
=======
        $this->addSql('ALTER TABLE annonces ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonces ADD CONSTRAINT FK_CB988C6FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_CB988C6FA76ED395 ON annonces (user_id)');
>>>>>>> origin/sandra:my_project_name/src/Migrations/Version20200206145613.php
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

<<<<<<< HEAD:my_project_name/src/Migrations/Version20200217080842.php
        $this->addSql('ALTER TABLE annonces DROP closed_at');
=======
        $this->addSql('ALTER TABLE annonces DROP FOREIGN KEY FK_CB988C6FA76ED395');
        $this->addSql('DROP INDEX IDX_CB988C6FA76ED395 ON annonces');
        $this->addSql('ALTER TABLE annonces DROP user_id');
>>>>>>> origin/sandra:my_project_name/src/Migrations/Version20200206145613.php
    }
}
