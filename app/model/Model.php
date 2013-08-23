<?php

namespace Model;

use Nette,
	Model\Datasource;


abstract class Model extends Nette\Object
{
	/** @var \Model\Datasource\ISource */
	private $datasource;
	
	
	/**
	 * @param \Model\Datasource\ISource $source
	 */
	public function __construct(Datasource\ISource $source) {
		$this->datasource = $source;
	}
	
	
	/**
	 * 
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->datasource, $name), $args);
	}
	
	
	/**
	 * @return \Model\Datasource\ISource
	 */
	public function getDataSource()
	{
		return $this->datasource;
	}
}
