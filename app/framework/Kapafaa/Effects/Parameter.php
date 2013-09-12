<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Modifications\Modification;


/**
 * @method string getParameter()
 * @method string getModification()
 */
class Parameter extends GameEffect
{
	/** @var string */
	private $parameter;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	private $modification;
	
	
	/**
	 * @param string $target
	 * @param string $multiply
	 * @param string $parameter
	 * @param string $modification
	 */
	public function __construct($target, $multiply, $parameter, Modification $modification)
	{
		parent::__construct($parameter, $target, $multiply);
		$this->parameter = $parameter;
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
