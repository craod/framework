<?php

namespace Craod\Api\Cli\Command\Cache;

use Craod\Api\Utility\DependencyInjector;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class for running a given fixture
 *
 * @package Craod\Api\Cli\Command\Cache
 */
class FlushCommand extends AbstractCommand {

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('cache:flush')
			->setDescription('Flush the cache')
			->addArgument('key', InputArgument::OPTIONAL, 'If given, the name of the key to flush.', NULL)
			->setHelp(<<<EOT
The <info>%command.name%</info> command clears the Redis cache:

    <info>%command.full_name%</info>

If a key is given, only the specified key is flushed

    <info>%command.full_name% key</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Flush the cache, all keys if no key is given
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$key = $input->getArgument('key');
		/** @var \Predis\Client $cache */
		$cache = DependencyInjector::get('cache');
		if ($key === NULL) {
			$output->write('<comment>Flushing all cache keys...</comment> ');
			$cache->flushall();
		} else {
			$output->write('<comment>Flushing cache key <info>' . $key . '</info>...</comment> ');
			$cache->set($key, NULL, 0);
		}
		$output->writeln('<info>Complete</info>');
	}
}
