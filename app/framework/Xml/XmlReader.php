<?php

namespace Framework\Xml;

use Nette;


class XmlReader extends Nette\Object
{
	
	public function __construct()
	{
		throw new Nette\StaticClassException;
	}
	
	
	/**
	 * @param string $filename
	 * @return \SimpleXMLElement
	 * @throws \Nette\InvalidArgumentException 
	 */
	public static function fromFile($filename)
	{
		if (!file_exists($filename) || !is_readable($filename)) {
			throw new Nette\InvalidArgumentException;
		}
		return simplexml_load_file($filename);
	}
}
