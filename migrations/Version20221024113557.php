<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221024113557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'move old database table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'RENAME TABLE plakat_orte TO plakat_orte_bak'
        );
    }

    public function down(Schema $schema): void
    {

        $this->addSql(
            'RENAME TABLE plakat_orte_bak TO plakat_orte'
        );
    }
}
