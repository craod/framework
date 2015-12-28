<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to handle categories
 *
 * @package Migrations
 */
class Version20151122000001 extends AbstractMigration {

	/**
	 * Create the category table
	 *
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$table = $schema->createTable('categories');
		$table->addColumn('guid', 'guid', ['unique' => TRUE]);
		$table->addColumn('created', 'datetimetz');
		$table->addColumn('active', 'boolean');
		$table->addColumn('name', 'string');
		$table->addColumn('author', 'guid');
		$table->addColumn('settings', 'jsonb');
		$table->addColumn('lastactivity', 'datetimetz');
		$table->setPrimaryKey(['guid']);
		$table->addForeignKeyConstraint('users', ['author'], ['guid'], ['cascade' => 'all'], 'categories_author');

		$table = $schema->createTable('categories_relations');
		$table->addColumn('parentcategory', 'guid');
		$table->addColumn('childcategory', 'guid');
		$table->addUniqueIndex(['parentcategory', 'childcategory'], 'combination_unique');
		$table->addForeignKeyConstraint('categories', ['parentcategory'], ['guid'], ['cascade' => 'all'], 'categories_relations_parent');
		$table->addForeignKeyConstraint('categories', ['childcategory'], ['guid'], ['cascade' => 'all'], 'categories_relations_child');
	}

	/**
	 * Remove the category table
	 *
	 * @param Schema $schema
	 */
	public function down(Schema $schema) {
		$schema->dropTable('categories');
		$schema->dropTable('categories_relations');
	}
}
