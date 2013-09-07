<?php

namespace Framework\Xml;

use Nette,
	Framework\Diagnostics\TimerPanel;


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
		TimerPanel::timer('game.xml load');
		if (NULL === ($xml = $cache->load($filename))) {
			TimerPanel::timer('game.xml parsing');
			$parser = new XmlParser;
			$parser->setFile($filename)->parse();
			$cache->save($filename, $xml = $parser->parsed, array(
				Nette\Caching\Cache::FILES => $filename
			));
			TimerPanel::timer('game.xml parsing');
		}
		TimerPanel::timer('game.xml load');
		return $xml;
	}
}
