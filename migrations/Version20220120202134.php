<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220120202134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Wahlkreis table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wahlkreis (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, geometry_type VARCHAR(255) NOT NULL, geometry_coordinates LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', agh_wahlkreis_long VARCHAR(255) DEFAULT NULL, agh_wahlkreis_short VARCHAR(255) DEFAULT NULL, agh_bezirk VARCHAR(255) DEFAULT NULL, btw_number INT NOT NULL, btw_name VARCHAR(255) NOT NULL, btw_state_name VARCHAR(255) NOT NULL, btw_state_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wahlkreis');
    }
}
