<?php

namespace Framework\Utils;


class SmartPropertyAccessor implements IPropertyAccessor
{
	public static function has($object, $name)
	{
		if (is_object($object) && $object instanceof \Framework\Utils\SmartObject) {
			return self::has($object->getObj(), $name);
		} else if (is_object($object) && $object instanceof \Nette\Database\Table\ActiveRow) {
            return array_key_exists($name, $object->toArray());
        } elseif (is_object($object) && $object instanceof \ArrayObject) {
            return $object->offsetExists($name);
        } elseif (is_object($object)) {
            return property_exists($object, $name);
        } elseif (is_array($object) || $object instanceof \ArrayAccess) {
            return array_key_exists($name, $object);
        } else {
            throw new \InvalidArgumentException('Please implement your own property accessor.');
        }
	}
	
	
    public static function get($object, $name)
	{
		return isset($object->$name) || (is_object($object) && property_exists($object, $name))
            ? $object->$name
            : $object[$name];
	}

	
    public static function set($object, $name, $value)
	{
		if (isset($object->$name) || (is_object($object) && property_exists($object, $name))) {
            $object->$name = $value;
        } else {
            $object[$name] = $value;
        }
	}
}
