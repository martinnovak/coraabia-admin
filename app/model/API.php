<?php

namespace Model;

use Nette;



class API extends Nette\Object
{
	/** @var \Model\Locales */
	private $locales;
	
	/** @var array */
	private $urls;
	
	
	
	/**
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Locales $locales, array $urls)
	{
		$this->locales = $locales;
		$this->urls = $urls;
	}
	
	
	
	/**
	 * @param array $params 
	 */
	public function query(array $params)
	{
		$data = json_encode((object)$params);
		
		Nette\Diagnostics\Debugger::log($data);
		
		$context = stream_context_create(array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $data
		)));
		
		$result = json_decode(file_get_contents($this->urls[$this->locales->server], FALSE, $context));
		if (!isset($result->status)) {
			$result->status = 'ERROR';
		}
		
		if ($result->status != 'OK') {
			if (!isset($result->message)) {
				$result->message = '';
			}
			throw new \LogicException("Request failed with status '$result->status' and message '$result->message'.");
		}
		return $result;
	}
	
	
	
	/**
	 * @param type $date 
	 */
	public function formatDate($date)
	{
		if ($date instanceof \DateTime) {
			return $date->format('Y-m-d\TH:i:s');
		} else if (is_integer($date)) {
			return date('Y-m-d\TH:i:s', $date);
		} else {
			$date = strtotime((string)$date, $this->locales->timestamp);
			if ($date === FALSE) {
				throw new Nette\InvalidArgumentException("");
			}
			return date('Y-m-d\TH:i:s', $date);
		}
	}
}
