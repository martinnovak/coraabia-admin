<?php

namespace Framework\Kapafaa;

use Nette;


/**
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
	
	
	/**
	 * @param int $id
	 * @return \Framework\Kapafaa\Script
	 */
	public function removeObject($id)
	{
		unset($this->objects[$id]);
		return $this;
	}
}
