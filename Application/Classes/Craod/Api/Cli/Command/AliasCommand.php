<?php

namespace Craod\Api\Cli\Command;

use Craod\Api\Exception\InvalidCommandException;
use Craod\Api\Utility\Settings;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * An alias command runs a set of commands loaded from the given CONFIGURATION_PATH
 *
 * @package Craod\Api\Cli\Command
 */
abstract class AliasCommand extends Command {

	const CONFIGURATION_PATH = 'Craod.Api.cli.commands';

	/**
	 * Run all given commands
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 * @throws InvalidCommandException
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		if (self::CONFIGURATION_PATH === FALSE) {
			throw new InvalidCommandException('No configuration path given for command ' . get_called_class(), 1448645083);
		}
		foreach (Settings::get(self::CONFIGURATION_PATH . '.' . get_called_class()) as $commandName) {
			$output->writeln('<comment>Running command <info>' . $commandName . '</info></comment>');
			$command = $this->getApplication()->find($commandName);
			$command->run($input, $output);
		}
	}
}
