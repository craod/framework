<?php

namespace Craod\Api\Cli;

use Craod\Api\Utility\DependencyInjector;
use Craod\Api\Utility\Settings;
use Craod\Api\Core\Application as CraodApplication;
use Doctrine\DBAL\Migrations\Tools\Console\Command as DoctrineCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Application as CliApplication;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Command line interface for the Craod API
 *
 * @package Craod\Api\Cli
 */
class Cli extends CliApplication implements CraodApplication {

	/**
	 * Cli constructor
	 */
	public function __construct() {
		parent::__construct('Craod Command Line Interface', \Doctrine\ORM\Version::VERSION);
		$this->initialize();
	}

	/**
	 * Initialize the class
	 *
	 * @return void
	 */
	public function initialize() {
		$helperSet = new HelperSet([
			'db' => new ConnectionHelper(DependencyInjector::get('database')),
			'dialog' => new DialogHelper(),
		]);

		$this->addCommandsFromSettings();
		$this->setHelperSet($helperSet);
	}

	/**
	 * Add the commands from the settings
	 *
	 * @return void
	 */
	public function addCommandsFromSettings() {
		foreach (Settings::get('Craod.Api.cli.commands') as $commandClassPath) {
			$this->add(new $commandClassPath());
		}
	}
}