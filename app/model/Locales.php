<?php

namespace Model;

use Nette;


/**
 * @method \Nette\DateTime getTimestamp()
 * @method array getLangs()
 */
class Locales extends Nette\FreezableObject
{
	/** @var string */
	private $lang;
	
	/** @var string */
	private $module;
	
	/** @var string */
	private $server;
	
	/** @var \Nette\DateTime */
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
		$this->timestamp = Nette\DateTime::from(time());
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
			$self->module = count($presenter) == 2 ? strtolower($presenter[0]) : FALSE;
			$self->setLang(strtolower($parameters['lang']));
			$self->server = isset($parameters['server']) ? strtolower($parameters['server']) : FALSE;
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
	 */
	public function getServer()
	{
		return strtolower($this->server);
	}
	
	
	/**
	 * @param string $lang
	 * @throws \Nette\InvalidArgumentException
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
	 */
	public function getLang()
	{
		return strtolower($this->lang);
	}
	
	
	/**
	 * @param mixed $timestamp
	 */
	public function setTimestamp($timestamp)
	{
		$this->updating();
		$this->timestamp = Nette\DateTime::from($timestamp);
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
	 */
	public function getModule()
	{
		return strtolower($this->module);
	}
}
