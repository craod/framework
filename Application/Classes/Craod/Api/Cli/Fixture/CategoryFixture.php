<?php

namespace Craod\Api\Cli\Fixture;

use Craod\Api\Model\Category;
use Craod\Api\Model\User;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixture to create test categories
 *
 * @package Craod\Api\Cli\Fixture
 */
class CategoryFixture extends AbstractFixture {

	/**
	 * @var string
	 */
	protected $description = 'Create test categories';

	/**
	 * Create a set of test categories
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function up(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Creating grandparent category... </comment>');
		$guid = $this->createGuidInRange(0);
		$repository = Category::getRepository();
		/** @var Category $grandparent */
		$grandparent = $repository->findOneBy(['guid' => $guid]);
		if ($grandparent !== NULL) {
			$grandparent->setActive(TRUE);
			$output->writeln('<comment>Already exists, setting to active</comment>');
		} else {
			/** @var User $user */
			$user = User::getRepository()->findOneBy(['active' => TRUE]);
			$grandparent = new Category();
			$grandparent->setActive(TRUE);
			$grandparent->setGuid($guid);
			$grandparent->setName('Test grandparent');
			$grandparent->setUser($user);
			$grandparent->setSettings([
				'fixture' => TRUE
			]);
			$output->writeln('<comment>Created</comment>');
		}
		$grandparent->updateLastActivity();
		$grandparent->save();

		$output->write('<comment>Creating parent category... </comment>');
		$guid = $this->createGuidInRange(1);
		$repository = Category::getRepository();
		/** @var Category $parent */
		$parent = $repository->findOneBy(['guid' => $guid]);
		if ($parent !== NULL) {
			$parent->setActive(TRUE);
			$output->writeln('<comment>Already exists, setting to active</comment>');
		} else {
			/** @var User $user */
			$user = User::getRepository()->findOneBy(['active' => TRUE]);
			$parent = new Category();
			$parent->setActive(TRUE);
			$parent->setGuid($guid);
			$parent->setName('Test parent');
			$parent->setUser($user);
			$parent->setSettings([
				'fixture' => TRUE
			]);
			$parent->addParent($grandparent);
			$output->writeln('<comment>Created</comment>');
		}
		$parent->updateLastActivity();
		$parent->save();

		$output->write('<comment>Creating child category... </comment>');
		$guid = $this->createGuidInRange(2);
		$repository = Category::getRepository();
		/** @var Category $category */
		$category = $repository->findOneBy(['guid' => $guid]);
		if ($category !== NULL) {
			$category->setActive(TRUE);
			$output->writeln('<comment>Already exists, setting to active</comment>');
		} else {
			/** @var User $user */
			$user = User::getRepository()->findOneBy(['active' => TRUE]);
			$category = new Category();
			$category->setActive(TRUE);
			$category->setGuid($guid);
			$category->setName('Test category');
			$category->setUser($user);
			$category->setSettings([
				'fixture' => TRUE
			]);
			$category->addParent($parent);
			$output->writeln('<comment>Created</comment>');
		}
		$category->updateLastActivity();
		$category->save();
	}

	/**
	 * Delete the created test users
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	public function down(InputInterface $input, OutputInterface $output) {
		$output->write('<comment>Deleting test categories... </comment>');
		$repository = Category::getRepository();
		$deleted = 0;
		$guidMask = str_replace('xxxx', '%', $this->settings['guidRange']);
		foreach ($repository->findLike(['guid' => $guidMask]) as $category) {
			/** @var Category $category */
			$category->delete();
			$deleted++;
		}
		$output->writeln('<comment>Deleted ' . $deleted . ' categories</comment>');
	}

	/**
	 * Generate a guid using the range in the fixture settings using the number given
	 *
	 * @param integer $number
	 * @return string
	 */
	protected function createGuidInRange($number) {
		$countPaddedToThousand = str_pad($number, 4, '0', STR_PAD_LEFT);
		return str_replace('xxxx', $countPaddedToThousand, $this->settings['guidRange']);
	}
}