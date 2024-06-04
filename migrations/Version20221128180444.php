<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221128180444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding new tables and columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE election_poster ADD thumbnail_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE election_poster DROP thumbnail_filename');
    }
}
