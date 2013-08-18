<?php

namespace Model;

use Nette,
	App;


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
	private $module;
	
	/** @var string */
	private $server;
	
	/** @var int */
	private $timestamp;
	
	/** @var array */
	private $staticUrls;
	
	/** @var array */
	private $langs;
	
	
	/**
	 * @param array $staticUrls
	 * @param array $langs 
	 */
	public function __construct(array $staticUrls, array $langs)
	{
		$this->staticUrls = $staticUrls;
		$this->langs = $langs;
		$this->timestamp = time();
	}
	
	
	/**
	 * @return string
	 * @throws Nette\OutOfRangeException 
	 */
	public function getStaticUrl()
	{
		if (!array_key_exists($this->server, $this->staticUrls)) {
			throw new Nette\OutOfRangeException("Statická URL pro server '{$this->server}' neexistuje.");
		}
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
			$presenter = explode(':', $request->presenterName);
			$self->module = count($presenter) == 2 ? $presenter[0] : FALSE;
			$self->setLang($parameters['lang']);
			$self->server = isset($parameters['server']) ? $parameters['server'] : FALSE;
			//$self->freeze();
		};
	}
	
	
	/**
	 * @param string $server 
	 */
	public function setServer($server)
	{
		$this->updating();
		$this->server = strtolower($server);
	}
	
	
	/**
	 * @return string
	 * @throws Nette\InvalidStateException 
	 */
	public function getServer()
	{
		/*if (!$this->server && $this->server !== FALSE) {
			throw new Nette\InvalidStateException("Locales nebyly inicializovány.");
		}*/
		return strtolower($this->server);
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
		$this->lang = strtolower($lang);
	}
	
	
	/**
	 * @return string
	 * @throws Nette\InvalidStateException 
	 */
	public function getLang()
	{
		/*if (!$this->lang) {
			throw new Nette\InvalidStateException("Locales nebyly inicializovány.");
		}*/
		return strtolower($this->lang);
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
	
	
	/**
	 * @param string $module
	 */
	public function setModule($module)
	{
		$this->updating();
		$this->module = strtolower($module);
	}
	
	
	/**
	 * @return string
	 * @throws \Nette\InvalidStateException 
	 */
	public function getModule()
	{
		/*if (!$this->module && $this->module !== FALSE) {
			throw new Nette\InvalidStateException("Locales nebyly inicializovány.");
		}*/
		return strtolower($this->module);
	}
}
