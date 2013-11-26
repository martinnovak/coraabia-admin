<?php

namespace Framework\Grido\PropertyAccessors;

use Grido,
	Framework;


class SmartPropertyAccessor extends Framework\Utils\SmartPropertyAccessor implements Grido\PropertyAccessors\IPropertyAccessor
{
	
	public static function hasProperty($object, $name) {
		return self::has($object, $name);
	}
	
	public static function getProperty($object, $name) {
		return self::get($object, $name);
	}
	
	public static function setProperty($object, $name, $value) {
		self::set($object, $name, $value);
	}
}
