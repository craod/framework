<?php

namespace Craod\Api\Cli\Command\Search;

use Craod\Api\Core\Bootstrap;
use Craod\Api\Exception\InvalidModelException;
use Craod\Api\Exception\ModelNotSearchableException;
use Craod\Api\Model\AbstractEntity;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Settings;
use Craod\Api\Rest\Annotation\Searchable;
use Craod\Api\Cli\Command\AbstractShellCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for indexing the table of a domain model on ElasticSearch
 *
 * @package Craod\Api\Cli\Command\Search
 */
class IndexCommand extends AbstractShellCommand {

	const MODEL_NAMESPACE = 'Craod\\Api\\Model\\';
	const SEARCHABLE_ANNOTATION_CLASS_PATH = Searchable::class;

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('search:index')
			->setDescription('Indexes the table of a given domain model')
			->addArgument('model', InputArgument::REQUIRED, 'The table for the domain model to index')
			->setHelp(<<<EOT
The <info>%command.name%</info> command runs the jdbc ElasticSearch indexer script for the table associated with the given domain model:

    <info>%command.full_name% model</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Create default users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 * @throws InvalidModelException
	 * @throws ModelNotSearchableException
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$jdbcPath = Bootstrap::ROOT_PATH . Settings::get('Craod.Api.elasticSearch.jdbc.path');
		$indexerPath = Bootstrap::ROOT_PATH . Settings::get('Craod.Api.elasticSearch.indexer.path');
		$databaseSettings = Settings::get('Craod.Api.database.settings');
		$model = $input->getArgument('model');
		$modelClassPath = self::MODEL_NAMESPACE . $model;
		if (!class_exists($modelClassPath) || !is_subclass_of($modelClassPath, AbstractEntity::class)) {
			throw new InvalidModelException('Invalid model class: ' . $modelClassPath, 1449360463);
		}

		$reader = Annotations::getReader();
		$searchable = $reader->getClassAnnotation(new \ReflectionClass($modelClassPath), self::SEARCHABLE_ANNOTATION_CLASS_PATH);
		if ($searchable === NULL) {
			throw new ModelNotSearchableException('Model class is not searchable: ' . $modelClassPath, 1449360464);
		}

		$output->write('<comment>Indexing table for model <info>' . $model . '</info>...</comment> ');
		$this->executeShellCommand('
			echo \'{
				"type" : "jdbc",
				"jdbc" : {
					"url" : "jdbc:postgresql://' . $databaseSettings['host'] . ':' . $databaseSettings['port'] . '/' . $databaseSettings['dbname'] . '?loglevel=0",
					"user" : "' . $databaseSettings['user'] . '",
					"password" : "' . $databaseSettings['password'] . '",
					"sql" : {
						"statement": "' . $indexerPath . $model . '.sql",
						"parameter": ["$now for created"]
					},
					"index_settings" : {
						"index" : {
							"number_of_shards" : 10
						}
					}
				}
			}\' | java
			-cp "' . $jdbcPath . '/lib/*"
			-Dlog4j.configurationFile=' . $jdbcPath . '/bin/log4j2.xml
			org.xbib.tools.Runner
			org.xbib.tools.JDBCImporter
		');
		$output->writeln('<comment>Complete</comment>');
	}
}
