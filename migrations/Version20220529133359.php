<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220529133359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update UUid for user';
    }

    public function up(Schema $schema): void
    {
        $users = $this->connection->executeQuery('SELECT * FROM user')->fetchAllAssociative();
        foreach ($users as $user) {
            $oldId = $user['id'];
            $this->connection->executeQuery(
                'UPDATE user set id=:id, roles = :roles where id=:idOld',
                [
                    'id' => Uuid::uuid4(),
                    'idOld' => $oldId,
                    'roles' => serialize(\json_decode($user['roles'], false, 512, JSON_THROW_ON_ERROR))
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
