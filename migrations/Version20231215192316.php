<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231215192316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding generic wahlkreis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis ADD generic_wahl_kreis_wahlkreis_short VARCHAR(255) DEFAULT NULL, ADD generic_wahl_kreis_name VARCHAR(255) DEFAULT NULL, ADD generic_wahl_kreis_nr INT DEFAULT NULL, ADD generic_wahl_kreis_wahlkreis_long VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE wahllokal CHANGE city city VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahllokal CHANGE city city VARCHAR(255) DEFAULT \'Berlin\' NOT NULL');
        $this->addSql('ALTER TABLE wahlkreis DROP generic_wahl_kreis_wahlkreis_long, DROP generic_wahl_kreis_wahlkreis_short, DROP generic_wahl_kreis_name, DROP generic_wahl_kreis_nr');
    }
}
