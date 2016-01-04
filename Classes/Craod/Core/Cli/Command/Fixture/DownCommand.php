<?php

namespace Craod\Core\Cli\Command\Fixture;

use Craod\Core\Utility\Settings;

use Craod\Core\Cli\Exception\InvalidFixtureException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class for running "down" in a given fixture
 *
 * @package Craod\Core\Cli\Command\Fixture
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
				->setDescription('Run the "down" action on the given fixture, or on all the fixtures')
				->addArgument('fixture', InputArgument::OPTIONAL, 'The name of the fixture to run down. If not provided, runs all fixtures', NULL)
				->setHelp(<<<EOT
The <info>%command.name%</info> command runs a fixture "down" function. The configured fixtures are found in the configuration path <info>{self::MAP_PATH}</info>:

    <info>%command.full_name% fixture</info>

If the fixture argument is not given, the "down" action is run on all fixtures instead according to the order given in <info>{self::ORDER_PATH}</info>:

    <info>%command.full_name%</info>
EOT
				);

		parent::configure();
	}

	/**
	 * Execute the "down" function on the fixture or fixtures
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		if ($input->getArgument('fixture') !== NULL) {
			$names = [$input->getArgument('fixture')];
		} else {
			$names = Settings::get(self::ORDER_PATH);
		}
		foreach ($names as $name) {
			$output->writeln('<comment>Executing <info>down</info> method in fixture <info>' . $name . '</info></comment>');
			try {
				$fixture = $this->getFixture($name);
				$fixture->down($input, $output);
			} catch (InvalidFixtureException $exception) {
				$output->writeln('<error>' . $exception->getMessage() . '</error>');
				break;
			}
		}
	}
}
