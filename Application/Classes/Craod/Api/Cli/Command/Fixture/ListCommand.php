<?php

namespace Craod\Api\Cli\Command\Fixture;

use Craod\Api\Utility\Settings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for listing fixtures
 *
 * @package Craod\Api\Cli\Command\Fixture
 */
class ListCommand extends AbstractFixtureCommand {

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('fixtures:list')
			->setDescription('List all the fixtures')
			->setHelp(<<<EOT
The <info>%command.name%</info> command shows a list of all fixtures as defined in the setting <info>{self::MAP_PATH}</info>:

    <info>%command.full_name% fixture</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Show a list of fixtures
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('<comment>Defined fixtures:</comment>');
		foreach ($this->getFixtureMap() as $name => $fixtureClassPath) {
			$output->write('  <info>' . str_pad($name, 20, ' ', STR_PAD_RIGHT) . '</info>');
			try {
				$fixture = $this->getFixture($name);
				$output->writeln($fixture->getDescription());
			} catch (\Exception $exception) {
				$output->writeln('<error>Invalid fixture</error>');
			}
		}
		$output->writeln('<comment>Order when calling "all"</comment>:');
		foreach (Settings::get(self::ORDER_PATH) as $index => $name) {
			$output->writeln('  <info>' . str_pad($index + 1, 20, ' ', STR_PAD_RIGHT) . '</info>' . $name);
		}
	}
}
