<?php

namespace Craod\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to handle users
 *
 * @package Craod\Api\Migrations
 */
class Version20151122000000 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 */
	public function up (Schema $schema) {
		$table = $schema->createTable('users');
		$table->addColumn('guid', 'guid', ['unique' => TRUE]);
		$table->addColumn('created', 'datetimetz');
		$table->addColumn('deleted', 'boolean');
		$table->addColumn('username', 'string', ['unique' => TRUE]);
		$table->addColumn('password', 'string');
		$table->addColumn('firstname', 'string');
		$table->addColumn('lastname', 'string');
		$table->addColumn('settings', 'jsonb');
		$table->setPrimaryKey(['guid']);
		$table->addIndex(['username'], 'username_index');
		$table->addIndex(['firstname', 'lastname'], 'fullname_index');
	}

	/**
	 * @param Schema $schema
	 */
	public function down (Schema $schema) {
		$schema->dropTable('users');
	}
}
