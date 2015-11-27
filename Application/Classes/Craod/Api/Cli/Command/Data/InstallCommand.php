<?php

namespace Craod\Api\Cli\Command\Data;

use Craod\Api\Cli\Command\AliasCommand;

/**
 * Class for running all install scripts
 *
 * @package Craod\Api\Cli\Command\Data
 */
class InstallCommand extends AliasCommand {

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
The <info>%command.name%</info> command runs all install commands:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}
}
