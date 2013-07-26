<?php

namespace Framework\Mapi;

use Nette;



class MapiObject implements \ArrayAccess {
	
	/** @var object */
	private $obj;
	
	
	
	/**
	 * @param object $obj
	 * @throws \Nette\InvalidArgumentException 
	 */
	public function __construct($obj)
	{
		if (!is_object($obj)) {
			throw new Nette\InvalidArgumentException("Argument must be an object.");
		}
		if ($obj instanceof MapiObject) {
			throw new Nette\InvalidArgumentException("Cannot double wrap MapiObject.");
		}
		$this->obj = $obj;
	}
	
	
	
	/**
	 * @param mixed $obj
	 * @return mixed 
	 */
	public static function access($obj)
	{
		if (is_object($obj) && !($obj instanceof MapiObject)) {
			$obj = new static($obj);
		} else if (is_array($obj)) {
			$obj = array_map(function ($item) {
				return \Framework\Mapi\MapiObject::access($item);
			}, $obj);
		}
		return $obj;
	}
	
	
	
	/**
	 * @return string 
	 */
	public function __toString()
	{
		return json_encode($this->obj);
	}
	
	
	
	/**
	 * @param string $name
	 * @return mixed
	 * @throws \Nette\InvalidArgumentException 
	 */
	public function __get($name)
	{
		if (!property_exists($this->obj, $name)) {
			throw new Nette\InvalidArgumentException("This MapiObject does not contain property '$name'.");
		}
		return self::access($this->obj->$name);
	}
	
	
	
	/**
	 * @param string $name
	 * @param mixed $value 
	 */
	public function __set($name, $value)
	{
		$this->obj->$name = $value;
	}
	
	
	
	/**
	 * @param string $name
	 * @return boolean 
	 */
	public function __isset($name)
	{
		return property_exists($this->obj, $name);
	}
	
	
	
	/**
	 * @param string $name 
	 */
	public function __unset($name)
	{
		unset($this->obj->$name);
	}
	
	
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @throws \Nette\InvalidArgumentException 
	 */
	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
			throw new Nette\InvalidArgumentException("Offset cannot be NULL.");
		}
		$this->obj->$offset = $value;
    }
	
	
	
	/**
	 * @param string $offset
	 * @return boolean 
	 */
    public function offsetExists($offset) {
        return property_exists($this->obj, $offset);
    }
	
	
	
	/**
	 * @param string $offset 
	 */
    public function offsetUnset($offset) {
        unset($this->obj->$offset);
    }
	
	
	
	/**
	 * @param string $offset
	 * @return mixed
	 * @throws \Nette\InvalidArgumentException 
	 */
    public function offsetGet($offset) {
		if (!property_exists($this->obj, $offset)) {
			throw new Nette\InvalidArgumentException("This MapiObject does not contain property '$offset'.");
		}
		return self::access($this->obj->$offset);
    }
}
