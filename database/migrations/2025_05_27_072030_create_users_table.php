<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Connection;

return new class {
    public function up(Connection $connection)
    {
        $schema = $connection->createSchemaManager();
        if (!$schema->tablesExist(['users'])) {
            $table = new \Doctrine\DBAL\Schema\Table('users');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
            $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
            $table->setPrimaryKey(['id']);
            $schema->createTable($table);
        }
    }

    public function down(Connection $connection)
    {
        $schema = $connection->createSchemaManager();
        if ($schema->tablesExist(['users'])) {
            $schema->dropTable('users');
        }
    }
};