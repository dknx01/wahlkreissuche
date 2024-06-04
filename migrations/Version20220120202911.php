<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220120202911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make btw columns nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis CHANGE btw_number btw_number INT DEFAULT NULL, CHANGE btw_name btw_name VARCHAR(255) DEFAULT NULL, CHANGE btw_state_name btw_state_name VARCHAR(255) DEFAULT NULL, CHANGE btw_state_number btw_state_number INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis CHANGE btw_number btw_number INT NOT NULL, CHANGE btw_name btw_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE btw_state_name btw_state_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE btw_state_number btw_state_number INT NOT NULL');
    }
}
