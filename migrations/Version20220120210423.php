<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220120210423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Synchronize entity and database definition';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plakat_orte CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE wahllokal CHANGE longitude longitude VARCHAR(255) NOT NULL, CHANGE latitude latitude VARCHAR(255) NOT NULL, CHANGE radius radius INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plakat_orte CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE wahllokal CHANGE longitude longitude VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE latitude latitude VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE radius radius INT DEFAULT 30 NOT NULL');
    }
}
