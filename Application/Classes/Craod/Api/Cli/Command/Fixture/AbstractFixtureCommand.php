<?php

namespace Craod\Api\Cli\Command\Fixture;

use Craod\Api\Cli\Fixture\AbstractFixture;
use Craod\Api\Exception\InvalidFixtureException;
use Craod\Api\Utility\Settings;

use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;

/**
 * Base fixture command class with common functions for interacting with fixtures
 *
 * @package Craod\Api\Cli\Command\Fixture
 */
abstract class AbstractFixtureCommand extends AbstractCommand {

	const CONFIGURATION_PATH = 'Craod.Api.cli.fixtures';

	/**
	 * Get a map of given fixture names to their class paths
	 *
	 * @return array
	 */
	protected function getFixtureMap() {
		return Settings::get(self::CONFIGURATION_PATH, []);
	}

	/**
	 * Get a map of given fixtures names to their classes
	 *
	 * @return array
	 */
	protected function getFixtures() {
		$fixtures = [];
		foreach ($this->getFixtureMap() as $name => $classPath) {
			$fixtures[$name] = $this->getFixture($name);
		}
		return Settings::get(self::CONFIGURATION_PATH, []);
	}

	/**
	 * Get a fixture by its name
	 *
	 * @return AbstractFixture
	 * @throws InvalidFixtureException
	 */
	protected function getFixture($name) {
		$fixtureMap = $this->getFixtureMap();
		if (!isset($fixtureMap[$name])) {
			throw new InvalidFixtureException('Fixture does not exist: ' . $name, 1448219528);
		}
		$classPath = $fixtureMap[$name];
		if (!$this->isFixture($classPath)) {
			throw new InvalidFixtureException('Invalid fixture provided: ' . $name, 1448219529);
		}
		return new $classPath();
	}

	/**
	 * Returns whether the classPath points to a valid fixture class
	 *
	 * @param string $classPath
	 * @return bool
	 */
	protected function isFixture($classPath) {
		return (class_exists($classPath) && is_subclass_of($classPath, AbstractFixture::class));
	}
}
