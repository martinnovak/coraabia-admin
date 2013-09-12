<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Modifications\Modification;


/**
 * @method string getModification()
 */
class GetRandomChampionsOnHand extends GameEffect
{
	const GET_RANDOM_CHAMPIONS_ON_HAND = 'getRandomChampionsOnHand';
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	private $modification;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $modification
	 */
	public function __construct($target, $multiply, Modification $modification)
	{
		parent::__construct(self::GET_RANDOM_CHAMPIONS_ON_HAND, $target, $multiply);
		$this->modification = $modification;
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type . ' ' . $this->modification;
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
