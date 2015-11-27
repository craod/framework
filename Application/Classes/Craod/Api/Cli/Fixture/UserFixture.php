<?php

namespace Craod\Api\Cli\Fixture;

use Craod\Api\Model\User;

use Craod\Api\Model\UserRole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixture to create test users
 *
 * @package Craod\Api\Cli\Fixture
 */
class UserFixture extends AbstractFixture {

	/**
	 * @var string
	 */
	protected $description = 'Create test users';

	/**
	 * Create a set of test users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function up(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Creating user <info>test@craod.com</info>... </comment>');
		$repository = User::getRepository();
		/** @var User $user */
		$user = $repository->findOneBy(['email' => 'test@craod.com']);
		if ($user !== NULL) {
			$output->writeln('<comment>Already exists, setting to active</comment>');
			$user->setActive(TRUE);
		} else {
			/** @var UserRole $userRole */
			$userRole = UserRole::getRepository()->findOneBy(['abbreviation' => 'test']);
			$user = new User();
			$user->setActive(TRUE);
			$user->setFirstName('Test');
			$user->setLastName('User');
			$user->setEmail('test@craod.com');
			$user->setPassword('password');
			$user->setSettings(['one' => 1]);
			$user->addUserRole($userRole);
			$output->writeln('<comment>Created</comment>');
		}
		$user->save();
	}

	/**
	 * Delete the created test users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function down(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Deleting user <info>test@craod.com</info>... </comment>');
		$repository = User::getRepository();
		/** @var User $user */
		$user = $repository->findOneBy(['email' => 'test@craod.com']);
		if ($user !== NULL) {
			$user->delete();
			$output->writeln('<comment>Done</comment>');
		} else {
			$output->writeln('<comment>Does not exist, not needed</comment>');
		}
	}
}