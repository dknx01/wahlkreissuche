<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230108154029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding city to Wahllokale';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahllokal ADD city VARCHAR(255) NOT NULL DEFAULT \'Berlin\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahllokal DROP city');
    }
}
