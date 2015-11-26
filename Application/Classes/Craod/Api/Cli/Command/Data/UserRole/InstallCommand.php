<?php

namespace Craod\Api\Cli\Command\Data\UserRole;

use Craod\Api\Model\UserRole;
use Craod\Api\Utility\Settings;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for installing user roles
 *
 * @package Craod\Api\Cli\Command\Data\UserRole
 */
class InstallCommand extends Command {

	const CONFIGURATION_PATH = 'Craod.Api.cli.data.userRole';

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('data:userrole:install')
			->setDescription('Install the user roles')
			->setHelp(<<<EOT
The <info>%command.name%</info> command installs the default user roles as defined in {self::CONFIGURATION_PATH}:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Install the default user roles
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		foreach (Settings::get(self::CONFIGURATION_PATH) as $abbreviation) {
			$output->write('<comment>Creating user role <info>' . $abbreviation . '</info>... </comment>');
			$userRole = UserRole::getRepository()->findOneBy(['abbreviation' => $abbreviation]);
			if ($userRole !== NULL) {
				$output->writeln('<comment>Already exists, setting to active</comment>');
				$userRole->setActive(TRUE);
			} else {
				$userRole = new UserRole();
				$userRole->setAbbreviation($abbreviation);
				$userRole->setActive(TRUE);
				$output->writeln('<info>Created</info>');
			}
			$userRole->save();
		}
	}
}
