<?php

namespace Craod\Api\Cli\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Class that provides methods for running shell commands
 *
 * @package Craod\Api\Cli\Command
 */
abstract class AbstractShellCommand extends Command {

	/**
	 * Executes the given shell command. Removes tabs and newlines and replaces them with spaces
	 *
	 * @param string
	 * @return string
	 */
	public function executeShellCommand($shellCommand) {
		return exec(trim(str_replace(["\n", "\r"], ' ', str_replace("\t", '', $shellCommand))));
	}
}
