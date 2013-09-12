<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Modifications\Modification;


/**
 * @method string getModification()
 */
class DuelWin extends GameEffect
{
	const DUEL_WIN = 'duelWin';
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	private $modification;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $modification
	 */
	public function __construct($target, $multiply, Modification $modification)
	{
		parent::__construct(self::DUEL_WIN, $target, $multiply);
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
