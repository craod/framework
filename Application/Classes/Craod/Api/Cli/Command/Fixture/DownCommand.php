<?php

namespace Craod\Api\Cli\Command\Fixture;

use Craod\Api\Exception\InvalidFixtureException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class for running "down" in a given fixture
 *
 * @package Craod\Api\Cli\Command\Fixture
 */
class DownCommand extends AbstractFixtureCommand {

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('fixtures:down')
			->setDescription('Run the given fixture down')
			->addArgument('fixture', InputArgument::REQUIRED, 'The name of the fixture to run down.', NULL)
			->setHelp(<<<EOT
The <info>%command.name%</info> command runs a fixture "down" function. The configured fixtures are found in the configuration path <info>{self::CONFIGURATION_PATH}</info>:

    <info>%command.full_name% fixture</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Execute the wanted fixture "down" function
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$name = $input->getArgument('fixture');
		$output->writeln('<comment>Executing <info>down</info> method in fixture <info>' . $name . '</info></comment>');
		try {
			$fixture = $this->getFixture($name);
			$fixture->down($input, $output);
		} catch (InvalidFixtureException $exception) {
			$output->writeln('<error>' . $exception->getMessage() . '</error>');
		}
	}
}
