<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220116185121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Session table';
    }

    public function up(Schema $schema): void
    {

        $this->addSql(<<<SQL
CREATE TABLE `sessions` (
    `sess_id` VARBINARY(256) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_lifetime` INTEGER UNSIGNED NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL,
    INDEX `sessions_sess_lifetime_idx` (`sess_lifetime`)
) COLLATE utf8mb4_bin, ENGINE = InnoDB;
SQL
);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `sessions`');
    }
}
