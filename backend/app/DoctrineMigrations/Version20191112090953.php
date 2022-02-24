<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191112090953 extends AbstractMigration
{
    private const SOURCE_ROLE = "ROLE_ADMIN";
    private const DEST_ROLE = "ROLE_TRIBE_MASTER";

    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->connection->beginTransaction();
        /** @var array[] $result */
        $result = $this->connection->fetchAll('SELECT * FROM fos_user');
        foreach ($result as $row) {
            $id = $row['id'];
            $rolesSerialized = $row['roles'];
            $roles = unserialize($rolesSerialized);
            $index = array_search(self::SOURCE_ROLE, $roles);
            if ($index !== false) {
                unset($roles[$index]);
                if (!in_array(self::DEST_ROLE, $roles)) {
                    $roles[] = self::DEST_ROLE;
                }
            }
            $rolesSerialized = serialize($roles);
            $this->connection->update('fos_user', [ 'roles' => $rolesSerialized ], [ 'id' => $id ]);
        }
        $this->connection->commit();
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
    }
}
