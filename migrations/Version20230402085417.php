<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402085417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE automations (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, alert_user_method VARCHAR(100) DEFAULT NULL, INDEX IDX_5C7573E89D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE automations ADD CONSTRAINT FK_5C7573E89D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE auto_requests ADD automations_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auto_requests ADD CONSTRAINT FK_BDA34AAEE5CDEBF2 FOREIGN KEY (automations_id) REFERENCES automations (id)');
        $this->addSql('CREATE INDEX IDX_BDA34AAEE5CDEBF2 ON auto_requests (automations_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto_requests DROP FOREIGN KEY FK_BDA34AAEE5CDEBF2');
        $this->addSql('ALTER TABLE automations DROP FOREIGN KEY FK_5C7573E89D86650F');
        $this->addSql('DROP TABLE automations');
        $this->addSql('DROP INDEX IDX_BDA34AAEE5CDEBF2 ON auto_requests');
        $this->addSql('ALTER TABLE auto_requests DROP automations_id');
    }
}
