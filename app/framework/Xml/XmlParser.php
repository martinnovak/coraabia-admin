<?php

namespace Framework\Xml;

use Nette;


class XmlParser extends Nette\Object
{
	/** @var resource */
	private $parser;
	
	/** @var string */
	private $filename;
	
	/** @var \Framework\Xml\XmlElement */
	public $parsed;
	
	/** @var \Framework\Xml\XmlElement */
	private $current;
	
	
	public function __construct()
	{
		$this->parser = xml_parser_create('UTF-8');	
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
		xml_set_element_handler($this->parser, array($this, 'tagStart'), array($this, 'tagEnd'));
        xml_set_character_data_handler($this->parser, array($this, 'tagContent'));
	}
	
	
	/**
	 * @param string $filename
	 * @return \Framework\Xml\XmlParser
	 */
	public function setFile($filename)
	{
		$this->filename = $filename;
		return $this;
	}
	
	
	/**
	 * @return \Framework\Xml\XmlParser
	 * @throws \Nette\InvalidStateException
	 * @throws \Nette\IOException
	 * @throws \LogicException
	 */
	public function parse()
	{
		if (!$this->filename) {
			throw new Nette\InvalidStateException('XmlParser nemá nastavený soubor.');
		}
		/*if (!is_readable($this->filename)) {
			throw new Nette\IOException("XmlParser nemůže otevřít soubor '" . $this->filename . "'.");
		}*/
		$this->parsed = $this->current = NULL;
		if (!xml_parse($this->parser, file_get_contents($this->filename), TRUE)) {
			$message = xml_error_string(xml_get_error_code($this->parser));
			$line = xml_get_current_line_number($this->parser);
			throw new \LogicException("Line $line: $message");
		}
		return $this;
	}
	
	
	/**
	 * @param resource $parser
	 * @param string $name
	 * @param array $attrs
	 */
	public function tagStart($parser, $name, array $attrs)
	{
		$el = new XmlElement($name, $attrs, $this->current);
		if ($this->parsed === NULL) {
			$this->parsed = $el;
		}
		if ($this->current !== NULL) {
			$this->current->children[] = $el;
		}
		$this->current = $el;
	}
	
	
	/**
	 * @param resource $parser
	 * @param string $name
	 */
	public function tagEnd($parser, $name)
	{
		$this->current = $this->current->parent;
	}
	
	
	/**
	 * @param resource $parser
	 * @param string $data
	 */
	public function tagContent($parser, $data)
	{
		$this->current->content .= $data;
	}
}
