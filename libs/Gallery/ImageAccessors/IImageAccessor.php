<?php

namespace Gallery\ImageAccessors;


interface IImageAccessor
{
	
	public static function getSrc(\Gallery\Gallery $control, $data);
	
	public static function getHref(\Gallery\Gallery $control, $data);
}
