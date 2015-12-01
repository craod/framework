<?php

namespace Craod\Api\Cli\Command\Data\User;

use Craod\Api\Model\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for running a given fixture
 *
 * @package Craod\Api\Cli\Command\Data\User
 */
class InstallCommand extends Command {

	/**
	 * Configure this command
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('data:user:install')
			->setDescription('Installs default users')
			->setHelp(<<<EOT
The <info>%command.name%</info> command installs default users:

    <info>%command.full_name%</info>
EOT
			);

		parent::configure();
	}

	/**
	 * Create default users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Creating user <info>test@craod.com</info>... </comment>');
		$repository = User::getRepository();
		/** @var User $user */
		$user = $repository->findOneBy(['email' => 'test@craod.com']);
		if ($user !== NULL) {
			$output->writeln('<comment>Already exists, setting to active</comment>');
			$user->setActive(TRUE);
		} else {
			$user = new User();
			$user->setActive(TRUE);
			$user->setFirstName('Test');
			$user->setLastName('User');
			$user->setEmail('test@craod.com');
			$user->setPassword('password');
			$user->setSettings(['one' => 1]);
			$user->addRole(User::ADMINISTRATOR);
			$output->writeln('<comment>Created</comment>');
		}
		$user->save();
	}
}
