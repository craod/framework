<?php

namespace Craod\Api\Cli\Fixture;

use Craod\Api\Model\UserRole;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixture to create test user roles
 *
 * @package Craod\Api\Cli\Fixture
 */
class UserRoleFixture extends AbstractFixture {

	/**
	 * @var string
	 */
	protected $description = 'Create test user roles';

	/**
	 * Create a set of test user roles
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function up(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Creating user role <info>test</info>... </comment>');
		$repository = UserRole::getRepository();
		/** @var UserRole $userRole */
		$userRole = $repository->findOneBy(['abbreviation' => 'test']);
		if ($userRole !== NULL) {
			$output->writeln('<comment>Already exists, setting to active</comment>');
			$userRole->setActive(TRUE);
		} else {
			$userRole = new UserRole();
			$userRole->setActive(TRUE);
			$userRole->setAbbreviation('test');
			$output->writeln('<comment>Created</comment>');
		}
		$userRole->save();
	}

	/**
	 * Delete the created test user roles
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function down(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Deleting use role <info>test</info>... </comment>');
		$repository = UserRole::getRepository();
		/** @var UserRole $userRole */
		$userRole = $repository->findOneBy(['abbreviation' => 'test']);
		if ($userRole !== NULL) {
			$userRole->delete();
			$output->writeln('<comment>Done</comment>');
		} else {
			$output->writeln('<comment>Does not exist, not needed</comment>');
		}
	}
}