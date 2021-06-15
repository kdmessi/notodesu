<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210612223735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, INDEX IDX_5387574A12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_contact (event_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_C9354F9771F7E88B (event_id), INDEX IDX_C9354F97E7A1254A (contact_id), PRIMARY KEY(event_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE event_contact ADD CONSTRAINT FK_C9354F9771F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_contact ADD CONSTRAINT FK_C9354F97E7A1254A FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_contact DROP FOREIGN KEY FK_C9354F9771F7E88B');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE event_contact');
    }
}
