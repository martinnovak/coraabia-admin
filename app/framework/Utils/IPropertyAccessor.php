<?php

namespace Framework\Utils;


interface IPropertyAccessor
{
	
    public static function has($object, $name);

    public static function get($object, $name);

    public static function set($object, $name, $value);
}
