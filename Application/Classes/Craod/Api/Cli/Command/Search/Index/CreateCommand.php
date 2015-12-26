<?php

namespace Craod\Api\Cli\Command\Search\Index;

use Craod\Api\Core\Bootstrap;
use Craod\Api\Model\AbstractEntity;
use Craod\Api\Model\SearchableEntity;
use Craod\Api\Exception\InvalidModelException;
use Craod\Api\Exception\ModelNotSearchableException;
use Craod\Api\Rest\Annotation\Api\Searchable;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Search;
use Craod\Api\Utility\Settings;

use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for creating an index with all the searchable models as types
 *
 * @package Craod\Api\Cli\Command\Search\Index
 */
class CreateCommand extends Command {

	const MODEL_NAMESPACE = 'Craod\\Api\\Model\\';
	const MODEL_PATH = Bootstrap::ROOT_PATH . 'Classes/Craod/Api/Model';

	/**
	 * @var CachedReader
	 */
	protected $reader;

	/**
	 * @var string
	 */
	protected $indexName;

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this->indexName = Search::getIndexName();
		$this
			->setName('search:index:create')
			->setDescription('Creates the craod index with its types and properties based on the annotations')
			->setHelp(<<<EOT
The <info>%command.name%</info> command searches all Searchable models and creates an index with the models as types and their
properties if they have the annotation:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Create the index
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 * @throws InvalidModelException
	 * @throws ModelNotSearchableException
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Creating index <info>' . $this->indexName . '</info>...</comment> ');
		$this->reader = Annotations::getReader();
		if ($this->createIndex()) {
			$output->writeln('<comment>Complete</comment>');
		} else {
			$output->writeln('<comment>Already exists</comment>');
		}
	}

	/**
	 * Create the craod index which will contain all the tables as types
	 *
	 * @return boolean
	 * @throws ModelNotSearchableException
	 */
	protected function createIndex() {
		$elasticSearchData = [
			'index' => $this->indexName,
			'body' => [
				'settings' => Settings::get('Craod.Api.search.settings', []),
				'mappings' => []
			]
		];

		foreach (glob(self::MODEL_PATH . '/*.php') as $potentialPath) {
			$classBaseName = basename($potentialPath, '.php');
			$modelClassPath = self::MODEL_NAMESPACE . $classBaseName;
			if (class_exists($modelClassPath) && is_subclass_of($modelClassPath, SearchableEntity::class)) {
				$elasticSearchData['body']['mappings'] = array_merge_recursive(
					$elasticSearchData['body']['mappings'],
					$this->createMappingForModel($modelClassPath)
				);
			}
		}

		Search::getClient()->indices()->create($elasticSearchData);
		return TRUE;
	}

	/**
	 * Create a mapping array for the given domain model based on the searchable annotations of its properties
	 *
	 * @param string $modelClassPath
	 * @return array
	 * @throws ModelNotSearchableException
	 */
	protected function createMappingForModel($modelClassPath) {
		/** @var SearchableEntity $modelClass */
		$modelClass = $modelClassPath;
		$reflectionModelClass = new \ReflectionClass($modelClassPath);
		/** @var ORM\Table $tableAnnotation */
		$tableAnnotation = $this->reader->getClassAnnotation($reflectionModelClass, ORM\Table::class);
		$tableName = $tableAnnotation->name;
		$mappingArray = [
			'enabled' => TRUE,
		    'properties' => []
		];

		foreach ($modelClass::getSearchableProperties() as $propertyName => $columnType) {
			$property = $reflectionModelClass->getProperty($propertyName);
			/** @var Searchable $propertyAnnotation */
			$propertyAnnotation = $this->reader->getPropertyAnnotation($property, Searchable::class);
			$mappingArray['properties'][strtolower($property->getName())] = self::createMappingArrayForColumnType($columnType, $propertyAnnotation);
		}

		return [
			$tableName => $mappingArray
		];
	}

	/**
	 * Create a mapping configuration array for the given column type optionally overriding the settings using the given searchable
	 * annotation object
	 *
	 * @param string $columnType
	 * @param Searchable $propertyAnnotation
	 * @return array
	 */
	protected static function createMappingArrayForColumnType($columnType, Searchable $propertyAnnotation) {
		$mappingArray = [];

		switch ($columnType) {
			default:
				if (is_subclass_of($columnType, AbstractEntity::class)) {
					$mappingArray['type'] = 'string';
				} else {
					$mappingArray['type'] = $columnType;
				}
				break;
			case 'datetimetz':
				$mappingArray['type'] = 'date';
				$mappingArray['format'] = 'yyyy-MM-dd HH:mm:ss';
				break;
			case 'guid';
				$mappingArray['type'] = 'string';
				break;
		}

		if (is_array($propertyAnnotation->value)) {
			$mappingArray = array_merge($mappingArray, $propertyAnnotation->value);
		}

		return $mappingArray;
	}
}