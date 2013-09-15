<?php

namespace Framework\Kapafaa;

use Nette;


/**
 * Cora.g
 * revision 6360
 * retrieved 11.9.2013
 * 
 * @method array getObjects()
 */
class Script extends Nette\Object
{
	/** @var array */
	private $objects = array();
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$script = array_merge(
				array('('),
				$this->objects,
				array(')')
		);
		return implode("\n", $script);
	}

	
	/**
	 * @param \Framework\Kapafaa\Object $object
	 * @return \Framework\Kapafaa\Script
	 */
	public function addObject(Object $object)
	{
		$this->objects[] = $object;
		return $this;
	}
}
