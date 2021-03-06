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
	 * @return $this
	 */
	public function setParam($name, $value)
	{
		$this->args[$name] = $value;
		return $this;
	}
	
	
	/**
	 * @return mixed
	 * @throws \LogicException 
	 */
	public function load()
	{
		$result = RestApi::call($this->url, $this->args);
		
		if (empty($this->retColumn)) {
			return $result;
		}
		
		if (!isset($result->status)) {
			$result->status = 'ERROR';
		}
		
		if ($result->status != 'OK') {
			if (!isset($result->message)) {
				$result->message = isset($result->errorMessage) ? $result->errorMessage : '';
			}
			throw new \LogicException("Požadavek selhal se statusem '$result->status' a zprávou '$result->message'.");
			return $result;
		}
		$origResult = $result;
		
		$retColumn = explode('.', $this->retColumn);
		while ($tmp = array_shift($retColumn)) {
			if (!isset($result->$tmp)) {
				throw new \LogicException("Výsledek neobsahuje sloupec '$this->retColumn'.");
				return $origResult;
			}
			$result = $result->$tmp;
		}
		
		return $result;
	}
	
	
	/**
	 * @param mixed $date 
	 * @return string 
	 */
	public static function formatDate($date)
	{
		if ($date instanceof \DateTime) {
			$date = $date->getTimestamp();
		} else if (!is_integer($date)) {
			$date = strtotime((string)$date);
		}
		return $date * 1000;
	}
}
