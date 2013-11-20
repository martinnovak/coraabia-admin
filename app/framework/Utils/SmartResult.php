<?php

namespace Framework\Utils;

use Nette;


/**
 * @method array getData()
 */
class SmartResult extends Nette\Object implements \Countable, \Iterator, \ArrayAccess
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
	
	
	/**
	 * @return int 
	 */
	public function count()
	{
		return count($this->data);
	}
	
	
	/**
	 * @return mixed 
	 */
	public function current()
	{
		return SmartObject::access(current($this->data));
	}
	
	
	/**
	 * @return mixed 
	 */
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
	
	
	/**
	 * @return boolean 
	 */
	public function valid()
	{
		return key($this->data) !== NULL;
	}
	
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @throws \Nette\InvalidStateException 
	 */
	public function offsetSet($offset, $value) {
        throw new Nette\NotSupportedException("SmartResult cannot be modified.");
    }
	
	
	/**
	 * @param mixed $offset
	 * @return boolean 
	 */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->data);
    }
	
	
	/**
	 * @param mixed $offset
	 * @throws \Nette\InvalidStateException 
	 */
    public function offsetUnset($offset) {
        throw new Nette\NotSupportedException("SmartResult cannot be modified.");
    }
	
	
	/**
	 * @param mixed $offset
	 * @return mixed 
	 */
    public function offsetGet($offset) {
        return array_key_exists($offset, $this->data) ? SmartObject::access($this->data[$offset]) : NULL;
    }
}
