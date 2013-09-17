<?php

namespace Framework\Kapafaa\Configs;


/**
 * @kapafaa config.max_progress(%value%)
 * @description Hodnota maximálního progressu
 */
class MaxProgress extends Config
{
	/** @var int */
	public $value;
	
	
	/**
	 * @param int $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}
}
