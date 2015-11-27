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
		$table = $schema->createTable('user_roles');
		$table->addColumn('guid', 'guid', ['unique' => TRUE]);
		$table->addColumn('created', 'datetimetz');
		$table->addColumn('active', 'boolean');
		$table->addColumn('abbreviation', 'string', ['unique' => TRUE]);
		$table->setPrimaryKey(['guid']);
		$table->addUniqueIndex(['abbreviation'], 'abbreviation_unique');

		$table = $schema->createTable('users');
		$table->addColumn('guid', 'guid', ['unique' => TRUE]);
		$table->addColumn('created', 'datetimetz');
		$table->addColumn('active', 'boolean');
		$table->addColumn('email', 'string', ['unique' => TRUE]);
		$table->addColumn('password', 'string');
		$table->addColumn('firstname', 'string');
		$table->addColumn('lastname', 'string');
		$table->addColumn('settings', 'jsonb');
		$table->setPrimaryKey(['guid']);
		$table->addUniqueIndex(['email'], 'email_unique');
		$table->addIndex(['firstname', 'lastname'], 'fullname_index');

		$table = $schema->createTable('users_user_roles_mm');
		$table->addColumn('users', 'guid', ['unique' => TRUE]);
		$table->addColumn('user_roles', 'guid', ['unique' => TRUE]);
		$table->addUniqueIndex(['users', 'user_roles'], 'combination_unique');
		$table->addForeignKeyConstraint('users', ['users'], ['guid'], ['cascade' => 'all'], 'users_user_roles_mm_users');
		$table->addForeignKeyConstraint('user_roles', ['user_roles'], ['guid'], ['cascade' => 'all'], 'users_user_roles_mm_user_roles');
	}

	/**
	 * Remove the user table
	 *
	 * @param Schema $schema
	 */
	public function down(Schema $schema) {
		$schema->dropTable('users_user_roles_mm');
		$schema->dropTable('users');
		$schema->dropTable('user_roles');
	}
}
