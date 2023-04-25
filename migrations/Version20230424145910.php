<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230424145910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE likedislike (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_39D118DBA76ED395 (user_id), INDEX IDX_39D118DBF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE likedislike ADD CONSTRAINT FK_39D118DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE likedislike ADD CONSTRAINT FK_39D118DBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit CHANGE etat etat INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likedislike DROP FOREIGN KEY FK_39D118DBA76ED395');
        $this->addSql('ALTER TABLE likedislike DROP FOREIGN KEY FK_39D118DBF347EFB');
        $this->addSql('DROP TABLE likedislike');
        $this->addSql('ALTER TABLE produit CHANGE etat etat INT NOT NULL');
    }
}
