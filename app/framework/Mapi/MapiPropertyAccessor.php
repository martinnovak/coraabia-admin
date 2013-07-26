<?php

namespace Framework\Mapi;

use Grido;



class MapiPropertyAccessor implements Grido\PropertyAccessors\IPropertyAccessor
{
	/**
     * @param mixed $object
     * @param string $name
     * @return boolean
     */
    public static function hasProperty($object, $name)
	{
		return isset($object->$name);
	}

	
	
    /**
     * @param mixed $object
     * @param string $name
     * @return mixed
     */
    public static function getProperty($object, $name)
	{
		return $object->$name;
	}

	
	
    /**
     * @param mixed $object
     * @param string $name
     * @param string $value
     */
    public static function setProperty($object, $name, $value)
	{
		$object->$name = $value;
	}
}
