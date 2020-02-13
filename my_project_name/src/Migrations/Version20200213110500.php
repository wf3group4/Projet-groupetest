<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200213110500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE signalement (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, annonce_id INT DEFAULT NULL, message LONGTEXT NOT NULL, INDEX IDX_F4B55114A76ED395 (user_id), INDEX IDX_F4B551148805AB2F (annonce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B551148805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id)');
        $this->addSql('ALTER TABLE annonces ADD prestataire_id INT DEFAULT NULL, ADD vues INT NOT NULL');
        $this->addSql('ALTER TABLE annonces ADD CONSTRAINT FK_CB988C6FBE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_CB988C6FBE3DB2B7 ON annonces (prestataire_id)');
        $this->addSql('ALTER TABLE avis ADD prenom VARCHAR(255) NOT NULL, ADD note INT NOT NULL, CHANGE email nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users ADD commission DOUBLE PRECISION DEFAULT NULL, ADD vues INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE signalement');
        $this->addSql('ALTER TABLE annonces DROP FOREIGN KEY FK_CB988C6FBE3DB2B7');
        $this->addSql('DROP INDEX IDX_CB988C6FBE3DB2B7 ON annonces');
        $this->addSql('ALTER TABLE annonces DROP prestataire_id, DROP vues');
        $this->addSql('ALTER TABLE avis ADD email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP nom, DROP prenom, DROP note');
        $this->addSql('ALTER TABLE users DROP commission, DROP vues');
    }
}