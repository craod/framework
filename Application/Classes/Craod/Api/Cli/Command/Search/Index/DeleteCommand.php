<?php

namespace Craod\Api\Cli\Command\Search\Index;

use Craod\Api\Core\Bootstrap;
use Craod\Api\Exception\InvalidModelException;
use Craod\Api\Exception\ModelNotSearchableException;
use Craod\Api\Rest\Annotation\Searchable;
use Craod\Api\Utility\Annotations;
use Craod\Api\Utility\Search;
use Craod\Api\Utility\Settings;

use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for creating the elasticsearch craod index
 *
 * @package Craod\Api\Cli\Command\Search\Index
 */
class DeleteCommand extends Command {

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
			->setName('search:index:delete')
			->setDescription('Deletes the craod index')
			->setHelp(<<<EOT
The <info>%command.name%</info> command deletes the craod elasticsearch index. This function will most likely not exist beyond initial
development:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Delete the index
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Deleting index <info>' . $this->indexName . '</info>...</comment> ');
		$client = Search::getClient();
		if ($client->indices()->exists(['index' => $this->indexName])) {
			$client->indices()->delete(['index' => $this->indexName]);
			$output->writeln('<comment>Complete</comment>');
		} else {
			$output->writeln('<comment>Does not exist</comment>');
		}
	}
}