<?php

namespace Coraabia\Mapi;

use Nette;



class MapiResult extends Nette\Object implements \Countable, \Iterator, \ArrayAccess
{
	/** @var array */
	private $data;
	
	
	
	/**
	 * @param array $data 
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	
	
	public function count()
	{
		return count($this->data);
	}
	
	
	
	public function current()
	{
		return MapiObject::access(current($this->data));
	}
	
	
	
	public function key()
	{
		return key($this->data);
	}
	
	
	
	public function next()
	{
		next($this->data);
	}
	
	
	
	public function rewind()
	{
		reset($this->data);
	}
	
	
	
	public function valid()
	{
		return key($this->data) !== NULL;
	}
	
	
	
	public function offsetSet($offset, $value) {
        throw new Nette\InvalidStateException("You cannot modify MapiResult.");
    }
	
	
	
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->data);
    }
	
	
	
    public function offsetUnset($offset) {
        throw new Nette\InvalidStateException("You cannot modify MapiResult.");
    }
	
	
	
    public function offsetGet($offset) {
        return array_key_exists($offset, $this->data) ? MapiObject::access($this->data[$offset]) : NULL;
    }
}
