<?php

namespace Coraabia\Mapi;

use Nette;



class MapiObject implements \ArrayAccess {
	
	/** @var object */
	private $obj;
	
	
	
	public function __construct($obj)
	{
		if (!is_object($obj)) {
			throw new Nette\InvalidArgumentException("Argument must be an object.");
		}
		$this->obj = $obj;
	}
	
	
	
	public static function access($obj)
	{
		if (is_object($obj) && !($obj instanceof MapiObject)) {
			$obj = new static($obj);
		}
		return $obj;
	}
	
	
	
	public function __toString()
	{
		return json_encode($this->obj);
	}
	
	
	
	public function __get($name)
	{
		if (!property_exists($this->obj, $name)) {
			throw new Nette\InvalidArgumentException("This MapiObject does not contain property '$name'.");
		}
		return self::access($this->obj->$name);
	}
	
	
	
	public function __set($name, $value)
	{
		$this->obj->$name = $value;
	}
	
	
	
	public function __isset($name)
	{
		return property_exists($this->obj, $name);
	}
	
	
	
	public function __unset($name)
	{
		unset($this->obj->$name);
	}
	
	
	
	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
			throw new Nette\InvalidArgumentException("Offset cannot be NULL.");
		}
		$this->obj->$offset = $value;
    }
	
	
	
    public function offsetExists($offset) {
        return property_exists($this->obj, $offset);
    }
	
	
	
    public function offsetUnset($offset) {
        unset($this->obj->$offset);
    }
	
	
	
    public function offsetGet($offset) {
		if (!property_exists($this->obj, $offset)) {
			throw new Nette\InvalidArgumentException("This MapiObject does not contain property '$offset'.");
		}
		return self::access($this->obj->$offset);
    }
}
