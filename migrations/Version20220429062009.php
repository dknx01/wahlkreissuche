<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220429062009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Moving data to new table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
insert into election_poster
               (id, address_city, address_state, created_at, address_address, address_district, active, description, address_latitude, address_longitude, created_by)
               select uuid, 'Berlin', 'Berlin', created_at, address, district, active, description, latitude, longitude, '' from plakat_orte
SQL
        );
    }

    public function down(Schema $schema): void
    {
       $this->addSql('DELETE FROM election_poster where id IN (SELECT id FROM plakat_orte)');
    }
}
