<?php

namespace Framework\Kapafaa\Effects;

use Nette;


/**
 * @method string getType()
 */
abstract class Effect extends Nette\Object
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
