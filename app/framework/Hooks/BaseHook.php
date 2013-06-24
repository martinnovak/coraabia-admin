<?php

namespace Framework\Hooks;

use Nette;



/**
 * @method array getArgs()
 * @method bool isPropagationStopped()
 * @method setPropagationStopped(bool)
 */
class BaseHook extends Nette\Object
{
	/** @var array */
	private $args;
	
	/** @var bool */
	private $propagationStopped = FALSE;
	
	
	
	public function __construct()
	{
		$this->args = func_get_args();
	}
}