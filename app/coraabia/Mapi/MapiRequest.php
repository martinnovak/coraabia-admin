<?php

namespace Coraabia\Mapi;

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
	 * @param array $params 
	 */
	public function load()
	{
		$data = json_encode((object)$this->args);
		
		$context = stream_context_create(array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $data
		)));
		
		$result = json_decode(file_get_contents($this->url, FALSE, $context));
		if (!isset($result->status)) {
			$result->status = 'ERROR';
		}
		
		if ($result->status != 'OK') {
			if (!isset($result->message)) {
				$result->message = '';
			}
			throw new \LogicException("Request failed with status '$result->status' and message '$result->message'.");
			return $result;
		}
		
		if (!isset($result->{$this->retColumn})) {
			throw new \LogicException("Result doesn't contain column '$this->retColumn'.");
			return $result;
		}
		
		//@todo get rid of this
		$final = array();
		foreach ($result->{$this->retColumn} as $object) {
			foreach ($object as $key => $value) {
				if (is_object($value)) {
					$object->$key = json_encode($value);
				}
			}
			$final[] = $object;
		}
		return $final;
	}
	
	
	
	/**
	 * @param type $date 
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
				throw new Nette\InvalidArgumentException("Cannot format date '$date'.");
			}
			return date('Y-m-d\TH:i:s', $date);
		}
	}
}
