<?php

namespace Craod\Api\Cli\Command\Fixture;

use Craod\Api\Exception\InvalidFixtureException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class for running "up" in a given fixture
 *
 * @package Craod\Api\Cli\Command\Fixture
 */
class UpCommand extends AbstractFixtureCommand {

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('fixtures:up')
			->setDescription('Run the given fixture up')
			->addArgument('fixture', InputArgument::REQUIRED, 'The name of the fixture to run up.', NULL)
			->setHelp(<<<EOT
The <info>%command.name%</info> command runs a fixture "up" function. The configured fixtures are found in the configuration path <info>{self::CONFIGURATION_PATH}</info>:

    <info>%command.full_name% fixture</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Execute the wanted fixture "up" function
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$name = $input->getArgument('fixture');
		$output->writeln('<comment>Executing <info>up</info> method in fixture <info>' . $name . '</info></comment>');
		try {
			$fixture = $this->getFixture($name);
			$fixture->up($input, $output);
		} catch (InvalidFixtureException $exception) {
			$output->writeln('<error>' . $exception->getMessage() . '</error>');
		}
	}
}
