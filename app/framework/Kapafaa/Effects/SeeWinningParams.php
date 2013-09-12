<?php

namespace Framework\Kapafaa\Effects;


class SeeWinningParams extends GameEffect
{
	const SEE_WINNING_PARAMS = 'see.winningParams';
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 */
	public function __construct($target, $multiply)
	{
		parent::__construct(self::SEE_WINNING_PARAMS, $target, $multiply);
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type;
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
