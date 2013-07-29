<?php

namespace Model;

use Nette;



/**
 * Locales.
 * @method int getTimestamp
 * @method getLangs()
 */
class Locales extends Nette\FreezableObject
{
	/** @var string */
	private $lang;
	
	/** @var string */
	private $server;
	
	/** @var int */
	private $timestamp;
	
	/** @var array */
	private $staticUrls;
	
	/** @var array */
	private $langs;
	
	
	
	public function __construct(array $staticUrls, array $langs)
	{
		$this->staticUrls = $staticUrls;
		$this->langs = $langs;
		$self->timestamp = time();
	}
	
	
	
	public function getStaticUrl()
	{
		return $this->staticUrls[$this->server];
	}
	
	
	
	/**
	 * @param \Nette\Application\Application $application 
	 */
	public function setupOnRequest(Nette\Application\Application $application)
	{
		$self = $this;
		$application->onRequest[] = function ($application, $request) use ($self) {
			$parameters = $request->getParameters();
			$self->setLang($parameters['lang']);
			$self->server = $parameters['server'];
			//$self->freeze();
		};
	}
	
	
	
	/**
	 * @param string $server 
	 */
	public function setServer($server)
	{
		$this->updating();
		$this->server = $server;
	}
	
	
	
	/**
	 * @return string
	 * @throws Nette\InvalidStateException 
	 */
	public function getServer()
	{
		if (!$this->server) {
			throw new Nette\InvalidStateException("Locales has not been initialized yet.");
		}
		return $this->server;
	}
	
	
	
	/**
	 * @param string $lang
	 */
	public function setLang($lang)
	{
		$this->updating();
		if (!in_array($lang, $this->langs)) {
			throw new Nette\InvalidArgumentException("Jazyk '$lang' není podporován.");
		}
		$this->lang = $lang;
	}
	
	
	
	/**
	 * @return string
	 * @throws Nette\InvalidStateException 
	 */
	public function getLang()
	{
		if (!$this->lang) {
			throw new Nette\InvalidStateException("Locales has not been initialized yet.");
		}
		return $this->lang;
	}
	
	
	
	/**
	 * @param int $timestamp
	 */
	public function setTimestamp($timestamp)
	{
		$this->updating();
		$this->timestamp = $timestamp;
	}
	
	
	
	/**
	 * @param array $langs
	 */
	public function setLangs(array $langs)
	{
		$this->updating();
		$this->langs = $langs;
	}
}
