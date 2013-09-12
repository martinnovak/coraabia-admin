<?php

namespace Framework\Kapafaa\Triggers;

use Nette;


/**
 * @method string getType()
 */
abstract class Trigger extends Nette\Object
{
	/** @var string */
	private $type;
	
	
	/**
	 * @param string $type
	 */
	public function __construct($type) {
		$this->type = $type;
	}
}
