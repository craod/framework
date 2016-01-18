<?php

namespace Craod\Core\Utility;

use Craod\Core\Model\AbstractEntity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * The casting utility
 *
 * @package Craod\Core\Utility
 */
class CastingUtility {

	const DATE = 'date';

	/**
	 * Cast the given string raw value to the necessary column type
	 *
	 * @param mixed $rawValue
	 * @param string $type
	 * @return mixed
	 */
	public static function castTo($rawValue, $type) {
		if ($type === self::DATE) {
			$value = new \DateTime($rawValue);
		} else if (strpos($type, 'Collection<') === 0) {
			/** @var AbstractEntity $collectionType */
			$collectionType = substr($type, strlen('Collection<'), -1);
			$collectionRepository = $collectionType::getRepository();
			$value = new ArrayCollection();
			foreach ($rawValue as $rawItem) {
				if (is_string($rawItem)) {
					$value->add($collectionRepository->findOneBy(['guid' => $rawItem]));
				} else {
					$value->add($rawItem);
				}
			}
		} else {
			$value = $rawValue;
		}
		return $value;
	}
}