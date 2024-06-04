<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221021140341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wish_election_poster (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', description LONGTEXT DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', active TINYINT(1) DEFAULT 1 NOT NULL, address_longitude DOUBLE PRECISION DEFAULT NULL, address_latitude DOUBLE PRECISION DEFAULT NULL, address_address VARCHAR(255) DEFAULT NULL, address_district VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) NOT NULL, address_state VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wish_election_poster');
    }
}
