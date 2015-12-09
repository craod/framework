<?php

namespace Craod\Api\Cli\Fixture;
use Craod\Api\Utility\Settings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Defines what a fixture should have in order to be able to run in our cli environment
 *
 * @package Craod\Api\Cli\Fixture
 */
abstract class AbstractFixture {

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Fixtures construct with their settings automatically injected
	 */
	public function __construct() {
		$this->settings = Settings::get('Craod.Api.cli.fixtures.settings.' . get_called_class(), []);
	}

	/**
	 * Execute this fixture
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	abstract public function up(InputInterface $input, OutputInterface $output);

	/**
	 * Cancel this fixture
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	abstract public function down(InputInterface $input, OutputInterface $output);

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
}