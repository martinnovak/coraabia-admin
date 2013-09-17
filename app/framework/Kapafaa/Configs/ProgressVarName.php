<?php

namespace Framework\Kapafaa\Configs;


/**
 * @kapafaa config.progress_var_name(#%value%#)
 * @description Proměnná maximálního progressu
 */
class ProgressVarName extends Config
{
	/** @var string */
	public $value;
	
	
	/**
	 * @param string $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}
}
