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
			throw new Nette\InvalidArgumentException("Argument musí být objekt.");
		}
		if ($obj instanceof MapiObject) {
			throw new Nette\InvalidArgumentException("MapiObject nelze zabalit do sebe.");
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
			throw new Nette\InvalidArgumentException("MapiObject neobsahuje sloupec '$name'.");
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
			throw new Nette\InvalidArgumentException("Offset nemůže být NULL.");
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
			throw new Nette\InvalidArgumentException("MapiObject neobsahuje sloupec '$offset'.");
		}
		return self::access($this->obj->$offset);
    }
}
