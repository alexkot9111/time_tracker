<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807082534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_history (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, created DATETIME NOT NULL, recipient_id_id INT NOT NULL, INDEX IDX_9A7A18842B6945EC (recipient_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE email_history ADD CONSTRAINT FK_9A7A18842B6945EC FOREIGN KEY (recipient_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD status INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_history DROP FOREIGN KEY FK_9A7A18842B6945EC');
        $this->addSql('DROP TABLE email_history');
        $this->addSql('ALTER TABLE `user` DROP status');
    }
}
