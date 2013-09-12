<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method string getTarget()
 * @method string getMultiply()
 */
abstract class GameEffect extends Effect
{
	const PREFIX = 'eff.gameplay';
	
	/** @var string */
	private $target;
	
	/** @var string */
	private $multiply;
	
	
	/**
	 * @param string $type
	 * @param string $target
	 */
	public function __construct($type, $target, $multiply) {
		parent::__construct($type);
		$this->target = $target;
		$this->multiply = $multiply;
	}
}
