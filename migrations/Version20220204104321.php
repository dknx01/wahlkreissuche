<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220204104321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Geometry data cleanup';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis DROP geometry_data_type, CHANGE geometry_type geometry_type VARCHAR(255) DEFAULT NULL, CHANGE geometry_data_geometry geometry_geometry GEOMETRY DEFAULT NULL COMMENT \'(DC2Type:geometry)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plakat_orte CHANGE longitude longitude VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE latitude latitude VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE district district VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE uuid uuid CHAR(36) DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE wahlkreis ADD geometry_data_type VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id id CHAR(36) NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE type type VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE agh_wahlkreis_long agh_wahlkreis_long VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE agh_wahlkreis_short agh_wahlkreis_short VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE agh_bezirk agh_bezirk VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE btw_name btw_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE btw_state_name btw_state_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE geometry_type geometry_type VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE geometry_coordinates geometry_coordinates LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE geometry_geometry geometry_data_geometry GEOMETRY DEFAULT NULL COMMENT \'(DC2Type:geometry)\'');
        $this->addSql('ALTER TABLE wahllokal CHANGE adress adress VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE longitude longitude VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE latitude latitude VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE district district VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
