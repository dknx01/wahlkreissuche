<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220529133203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alter user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plakat_orte CHANGE CREATED_AT created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE DELETED_AT deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plakat_orte CHANGE created_at CREATED_AT DATETIME NOT NULL COMMENT \'(DC2TYPE:DATETIME_IMMUTABLE)\', CHANGE deleted_at DELETED_AT DATETIME DEFAULT NULL COMMENT \'(DC2TYPE:DATETIME_IMMUTABLE)\'');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
