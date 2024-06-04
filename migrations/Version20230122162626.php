<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230122162626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding Wahllokaltour tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tour_points (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', points INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour_points_visited_pubs (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tour_points_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, INDEX IDX_FD900C516023C960 (tour_points_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tour_points_visited_pubs ADD CONSTRAINT FK_FD900C516023C960 FOREIGN KEY (tour_points_id) REFERENCES tour_points (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tour_points_visited_pubs DROP FOREIGN KEY FK_FD900C516023C960');
        $this->addSql('DROP TABLE tour_points');
        $this->addSql('DROP TABLE tour_points_visited_pubs');
    }
}
