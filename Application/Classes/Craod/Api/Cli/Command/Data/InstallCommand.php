<?php

namespace Craod\Api\Cli\Command\Data;

use Craod\Api\Utility\Settings;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for running a given fixture
 *
 * @package Craod\Api\Cli\Command\Data
 */
class InstallCommand extends Command {

	const CONFIGURATION_PATH = 'Craod.Api.cli.data.install';

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('data:install')
			->setDescription('Run all install commands')
			->setHelp(<<<EOT
The <info>%command.name%</info> command runs all install commands in the order specified in path {self::CONFIGURATION_PATH}:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Run all install commands
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		foreach (Settings::get(self::CONFIGURATION_PATH) as $commandName) {
			$commandName = 'data:' . $commandName . ':install';
			$output->writeln('<comment>Running command <info>' . $commandName . '</info></comment>');
			$command = $this->getApplication()->find($commandName);
			$command->run($input, $output);
		}
	}
}
