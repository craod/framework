<?php

namespace Craod\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to handle users and roles
 *
 * @package Craod\Api\Migrations
 */
class Version20151122000000 extends AbstractMigration {

	/**
	 * Create the user table
	 *
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$table = $schema->createTable('users');
		$table->addColumn('guid', 'guid', ['unique' => TRUE]);
		$table->addColumn('created', 'datetimetz');
		$table->addColumn('active', 'boolean');
		$table->addColumn('roles', 'integer', ['default' => 0]);
		$table->addColumn('email', 'string', ['unique' => TRUE]);
		$table->addColumn('password', 'string');
		$table->addColumn('firstname', 'string');
		$table->addColumn('lastname', 'string');
		$table->addColumn('settings', 'jsonb');
		$table->addColumn('token', 'string');
		$table->addColumn('lastaccess', 'datetimetz');
		$table->setPrimaryKey(['guid']);
		$table->addUniqueIndex(['email'], 'email_unique');
		$table->addIndex(['firstname', 'lastname'], 'fullname_index');
	}

	/**
	 * Remove the user table
	 *
	 * @param Schema $schema
	 */
	public function down(Schema $schema) {
		$schema->dropTable('users');
	}
}
