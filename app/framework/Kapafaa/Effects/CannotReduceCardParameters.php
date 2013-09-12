<?php

namespace Framework\Kapafaa\Effects;


class CannotReduceCardParameters extends GameEffect
{
	const CANNOT_REDUCE_CARD_PARAMETERS = 'cannotReduceCardParameters';
	
	
	public function __construct($target, $multiply)
	{
		parent::__construct(self::CANNOT_REDUCE_CARD_PARAMETERS, $target, $multiply);
	}
	
	
	public function __toString() {
		$result = self::PREFIX . '(' . $this->target . '.' . $this->type;
		if ($this->multiply) {
			$result .= ', multiply.' . $this->multiply;
		}
		return $result .= ')';
	}
}
