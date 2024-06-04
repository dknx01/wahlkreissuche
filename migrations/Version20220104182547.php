<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220104182547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding Wahllokale table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE if not exists wahllokal (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, radius INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wahllokal');
    }
}
