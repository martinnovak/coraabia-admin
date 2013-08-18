<?php

namespace Framework\Xml;

use Nette;


class XmlElement extends Nette\Object
{
	public $name;
	
	public $attrs;
	
	public $parent;
	
	public $children;
	
	public $content;
	
	
	/**
	 * @param string $name
	 * @param array $attrs
	 * @param \Framework\Xml\XmlElement|NULL $parent
	 */
	public function __construct($name, array $attrs = array(), $parent = NULL)
	{
		$this->name = $name;
		$this->attrs = $attrs;
		$this->parent = $parent;
		$this->children = array();
		$this->content = '';
	}
	
	
	public function getByName($name)
	{
		$result = array_filter($this->children, function ($item) use ($name) {
			return $item->name == $name;
		});
		$size = count($result);
		if (!$size) {
			return NULL;
		} else if ($size == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}
	
	
	public function getByAttr($id, $value)
	{
		$result = array_filter($this->children, function ($item) use ($id, $value) {
			return isset($item->attrs[$id]) && $item->attrs[$id] == $value;
		});
		$size = count($result);
		if (!$size) {
			return NULL;
		} else if ($size == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}
}
