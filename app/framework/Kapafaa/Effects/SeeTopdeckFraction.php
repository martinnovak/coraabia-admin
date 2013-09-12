<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method string getOtherTarget()
 */
class SeeTopdeckFraction extends GameEffect
{
	const SEE_TOPDECK_FRACTION = 'see.topDeckFraction';
	
	/** @var string */
	private $otherTarget;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $otherTarget
	 */
	public function __construct($target, $multiply, $otherTarget)
	{
		parent::__construct(self::SEE_TOPDECK_FRACTION, $target, $multiply);
		$this->otherTarget = $otherTarget;
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type . '.' . $this->otherTarget;
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
