<?php

namespace Framework\Kapafaa;

use Nette;


class ObjectFactory extends Nette\Object
{
	
	public function __construct() {
		throw new Nette\StaticClassException;
	}
	
	
	/**
	 * @param string $activityId
	 * @return \Framework\Kapafaa\Effects\GenericLocal
	 */
	public static function getActivityPlayableSetter($activityId)
	{
		return new Effects\GenericLocal(
				substr($activityId . '_PL', -20)
				, new Modifications\Number(
						new Operators\Equals(),
						1)
				);
	}
	
	
	/**
	 * @param string $activityId
	 * @return \Framework\Kapafaa\Effects\GenericLocal
	 */
	public static function getActivityFinishedSetter($activityId)
	{
		return new Effects\GenericLocal(
				substr($activityId . '_FI', -20)
				, new Modifications\Number(
						new Operators\Equals(),
						1)
				);
	}
}