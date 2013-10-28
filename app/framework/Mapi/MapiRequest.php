<?php

namespace Framework\Mapi;

use Nette;


class MapiRequest extends Nette\Object
{
	/** @var array */
	private $args;
	
	/** @var string */
	private $url;
	
	/** @var string */
	private $retColumn;
	
	
	/**
	 * @param string $url
	 * @param array $args 
	 * @param string $retColumn
	 */
	public function __construct($url, array $args, $retColumn)
	{
		$this->url = $url;
		$this->args = $args;
		$this->retColumn = $retColumn;
	}
	
	
	/**
	 * @param string $name
	 * @param string $value 
	 */
	public function setParam($name, $value)
	{
		$this->args[$name] = $value;
	}
	
	
	/**
	 * @return mixed
	 * @throws \LogicException 
	 */
	public function load()
	{
		$result = RestApi::call($this->url, json_encode((object)$this->args));
		
		if (!isset($result->status)) {
			$result->status = 'ERROR';
		}
		
		if ($result->status != 'OK') {
			if (!isset($result->message)) {
				$result->message = '';
			}
			throw new \LogicException("Požadavek selhal se statusem '$result->status' a zprávou '$result->message'.");
			return $result;
		}
		
		if (!isset($result->{$this->retColumn})) {
			throw new \LogicException("Výsledek neobsahuje sloupec '$this->retColumn'.");
			return $result;
		}
		
		return $result->{$this->retColumn};
	}
	
	
	/**
	 * @param mixed $date 
	 * @return string 
	 */
	public static function formatDate($date)
	{
		if ($date instanceof \DateTime) {
			return $date->format('Y-m-d\TH:i:s');
		} else if (is_integer($date)) {
			return date('Y-m-d\TH:i:s', $date);
		} else {
			$date = strtotime((string)$date);
			if ($date === FALSE) {
				throw new Nette\InvalidArgumentException("Datum '$date' nelze naformátovat.");
			}
			return date('Y-m-d\TH:i:s', $date);
		}
	}
}
