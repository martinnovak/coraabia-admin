<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Modifications\Modification;


/**
 * @method string getOtherTarget()
 * @method \Framework\Kapafaa\Modifications\Modification getModification()
 */
class SeeDuel extends GameEffect
{
	const SEE_DUEL = 'see.duel';
	
	/** @var string */
	private $otherTarget;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	private $modification;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $otherTarget
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 */
	public function __construct($target, $multiply, $otherTarget, Modification $modification)
	{
		parent::__construct(self::SEE_DUEL, $target, $multiply);
		$this->otherTarget = $otherTarget;
		$this->modification = $modification;
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type . '.' . $this->otherTarget . ' ' . $this->modification;
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
