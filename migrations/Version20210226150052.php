<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210226150052 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE learner (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(100) NOT NULL, reference_number VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, language VARCHAR(255) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, entity_id INT DEFAULT 1, manager_id INT DEFAULT NULL, enabled TINYINT(1) DEFAULT \'1\', enabled_from DATE DEFAULT NULL, enabled_until DATE DEFAULT NULL, custom_fields JSON DEFAULT NULL, UNIQUE INDEX UNIQ_8EF3834AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE learner');
    }
}
