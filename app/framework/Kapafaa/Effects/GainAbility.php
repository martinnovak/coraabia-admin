<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method string getAbility()
 */
class GainAbility extends GameEffect
{
	const GAIN_ABILITY = 'gainAbility';
	
	/** @var string */
	private $ability;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $ability
	 */
	public function __construct($target, $multiply, $ability)
	{
		parent::__construct(self::GAIN_ABILITY, $target, $multiply);
		$this->ability = $ability;
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type . '(@' . $this->ability . '@)';
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
