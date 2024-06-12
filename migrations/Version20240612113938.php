<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612113938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tracker (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL, user_id INT NOT NULL, company_id INT NOT NULL, INDEX IDX_AC632AAFA76ED395 (user_id), INDEX IDX_AC632AAF979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tracker_period (id INT AUTO_INCREMENT NOT NULL, tracker_start DATETIME NOT NULL, tracker_stop DATETIME DEFAULT NULL, tracker_id INT NOT NULL, INDEX IDX_F270D7BAFB5230B (tracker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created DATETIME NOT NULL, company_id INT NOT NULL, INDEX IDX_8D93D649979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tracker ADD CONSTRAINT FK_AC632AAFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tracker ADD CONSTRAINT FK_AC632AAF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE tracker_period ADD CONSTRAINT FK_F270D7BAFB5230B FOREIGN KEY (tracker_id) REFERENCES tracker (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tracker DROP FOREIGN KEY FK_AC632AAFA76ED395');
        $this->addSql('ALTER TABLE tracker DROP FOREIGN KEY FK_AC632AAF979B1AD6');
        $this->addSql('ALTER TABLE tracker_period DROP FOREIGN KEY FK_F270D7BAFB5230B');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE tracker');
        $this->addSql('DROP TABLE tracker_period');
        $this->addSql('DROP TABLE `user`');
    }
}
