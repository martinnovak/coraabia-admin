<?php

namespace Framework\Mapi;

use Nette,
	Framework;


class RestApi extends Nette\Object
{
	
	public function __construct()
	{
		throw new Nette\StaticClassException;
	}


	/**
	 * @param string $url
	 * @param array $data
	 * @return mixed
	 */
	public static function call($url, array $data)
	{
		$obj = (object)$data;
		
		Framework\Diagnostics\RestPanel::log($obj);
		Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		
		$context = stream_context_create(array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => json_encode($obj)
		)));
		
		$result = json_decode(file_get_contents($url, FALSE, $context));
		
		Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		Framework\Diagnostics\RestPanel::log($result);
		
		return $result;
	}
}
