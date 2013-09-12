<?php

namespace Framework\Kapafaa\Configs;

use Nette;


/**
 * @method string getType()
 * @method int|string getValue()
 */
abstract class Config extends Nette\Object
{
	const PREFIX = 'config';
	
	/** @var string */
	private $type;
	
	/** @var int|string */
	private $value;
	
	
	/**
	 * @param string $type
	 * @param int|string $value
	 */
	public function __construct($type, $value) {
		$this->type = $type;
		$this->value = $value;
	}
}
