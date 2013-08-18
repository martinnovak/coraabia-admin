<?php

namespace Model;

use Nette,
	Framework;


class XmlFactory extends Nette\Object
{
	/** @var \Nette\Caching\IStorage */
	private $storage;
	
	
	/**
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct(Nette\Caching\IStorage $storage) {
		$this->storage = $storage;
	}
	
	
	/**
	 * @param string $filename
	 * @return \Framework\Xml\XmlElement
	 */
	public function createXml($filename)
	{
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class()));
		if (NULL === ($xml = $cache->load($filename))) {
			$parser = new Framework\Xml\XmlParser;
			$parser->setFile($filename)->parse();
			$cache->save($filename, $xml = $parser->parsed, array(
				Nette\Caching\Cache::FILES => $filename
			));
		}
		return $xml;
	}
}
