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
		$json = json_encode((object)$data);
		
		Framework\Diagnostics\RestPanel::log($json);
		Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		
		$context = stream_context_create(array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $json
		)));
		
		$result = file_get_contents($url, FALSE, $context);
		
		Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		Framework\Diagnostics\RestPanel::log($json, $result);
		
		return json_decode($result);
	}
}
