<?php

namespace Framework\Kapafaa\Configs;


/**
 * @kapafaa config.max_progress(%value%)
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
