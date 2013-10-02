<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Modifications\Modification;


/**
 * @kapafaa eff.world.gen.local(#%variable%# %modification%)
 * @description Lokální proměnná
 */
class GenericLocal extends Effect
{
	/** @var string */
	public $variable;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	
	/**
	 * @param string
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 */
	public function __construct($variable, Modification $modification) {
		$this->variable = $variable;
		$this->modification = $modification;
	}
}
