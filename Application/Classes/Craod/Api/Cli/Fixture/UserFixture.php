<?php

namespace Craod\Api\Cli\Fixture;

use Craod\Api\Model\User;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Faker\Factory;

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
		$output->write('<comment>Creating ' . $this->settings['usersToCreate'] . ' users... </comment>');
		$faker = Factory::create();
		$repository = User::getRepository();
		$created = 0;
		for ($count = 0; $count < $this->settings['usersToCreate']; $count++) {
			$countPaddedToThousand = str_pad($count, 4, '0', STR_PAD_LEFT);
			$guid = str_replace('xxxx', $countPaddedToThousand, $this->settings['guidRange']);
			/** @var User $user */
			$user = $repository->findOneBy(['guid' => $guid]);
			if ($user !== NULL) {
				$user->setActive(TRUE);
			} else {
				$user = new User();
				$user->setActive(TRUE);
				$user->setGuid($guid);
				$user->setFirstName($faker->firstName);
				$user->setLastName($faker->lastName);
				$user->setEmail($faker->email);
				$user->setPassword($guid);
				$user->setSettings([
					'faker' => TRUE,
					'fakerNumber' => $created,
					'fixture' => TRUE
				]);
				$created++;
			}
			$user->save();
		}
		$output->writeln('<comment>Created ' . $created . ' users</comment>');
	}

	/**
	 * Delete the created test users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function down(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Deleting faker users... </comment>');
		$repository = User::getRepository();
		$deleted = 0;
		$guidMask = str_replace('xxxx', '%', $this->settings['guidRange']);
		foreach ($repository->findLike(['guid' => $guidMask]) as $user) {
			/** @var User $user */
			$user->delete();
			$deleted++;
		}
		$output->writeln('<comment>Deleted ' . $deleted . ' users</comment>');
	}
}