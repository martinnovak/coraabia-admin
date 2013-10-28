<?php

namespace Framework\Mapi;

use Nette;


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
		$context = stream_context_create(array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => json_encode((object)$data)
		)));
		
		return json_decode(file_get_contents($url, FALSE, $context));
	}
}