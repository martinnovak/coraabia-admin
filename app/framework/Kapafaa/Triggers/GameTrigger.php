<?php

namespace Framework\Kapafaa\Triggers;


/**
 * @method string getTarget()
 */
abstract class GameTrigger extends Trigger
{
	const PREFIX = 'trigger.gameplay';
	
	/** @var string */
	private $target;
	
	
	/**
	 * @param string $type
	 * @param string $target
	 */
	public function __construct($type, $target) {
		parent::__construct($type);
		$this->target = $target;
	}
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return self::PREFIX . '.' . $this->target . '.' . $this->type;
	}
}
