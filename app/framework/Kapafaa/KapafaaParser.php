<?php

namespace Framework\Kapafaa;

use Nette;


/**
 * @method array getClasses()
 */
class KapafaaParser extends Nette\Object
{
	/** @var \Nette\DI\Container */
	private $container;
	
	/** @var \Nette\Caching\IStorage */
	private $storage;
	
	/** @var array */
	private $classes;
	
	/** @var array */
	private $implementors;
	
	
	/**
	 * @param \Nette\DI\Container $container
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct(Nette\DI\Container $container, Nette\Caching\IStorage $storage)
	{
		$this->container = $container;
		$this->storage = $storage;
	}
	
	
	/**
	 * @param boolean $rebuild
	 * @return \Framework\Kapafaa\KapafaaParser
	 */
	public function loadClassData($rebuild = FALSE)
	{
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class()));
		$indexed = $this->container->getService('robotLoader')->getIndexedClasses();
		if ($rebuild) {
			$cache->remove($indexed);
		}
		if (($this->classes = $cache->load($indexed)) === NULL) {
			$this->classes = $this->build($indexed);
			$cache->save($indexed, $this->classes, array(
				Nette\Caching\Cache::FILES => array_map(function ($item) {
					return $item['file'];
				}, $this->classes)
			));
		}
		return $this;
	}
	
	
	/**
	 * @param array $indexed
	 * @return array
	 */
	protected function build(array $indexed)
	{
		$classes = array();
		foreach ($indexed as $name => $file) {
			$rc = \Nette\Reflection\ClassType::from($name);
			if (!($rc->hasAnnotation('kapafaa') && $rc->isSubclassOf('\Framework\Kapafaa\Object'))) {
				continue;
			}
			
			$kapafaa = $rc->getAnnotation('kapafaa');
			$parsed = $this->getRegular($rc, $kapafaa);
			$parent = $rc->getParentClass();
			
			$classes[$name] = array(
				'kapafaa' => str_replace('#', '@', $kapafaa),
				'regular' => $parsed[1],
				'params' => $parsed[0],
				'parent' => $parent ? $parent->getName() : '',
				'file' => $file
			);
		}
		return $classes;
	}
	
	
	/**
	 * @param \Nette\Reflection\ClassType $rc
	 * @param string $kapafaa
	 * @return array
	 * @throws \Framework\Kapafaa\KapafaaException
	 */
	protected function getRegular(Nette\Reflection\ClassType $rc, $kapafaa)
	{
		$params = array();
		$regular = preg_replace_callback('/%([a-z]+)%/i', function ($item) use ($rc, &$params) {
			if (!$rc->hasProperty($item[1])) {
				throw new KapafaaExcepion("Missing property '{$item[1]}' in " . $rc->getName() . ".");
			}
			$rp = $rc->getProperty($item[1]);
			if (!$rp->hasAnnotation('var')) {
				throw new KapafaaException("Property '" . $rc->getName() . "->{$item[1]}' must have @var annotation.");
			}
			list($type, ) = preg_split('/\s+/', trim($rp->getAnnotation('var')));
			$params[] = $type;
			switch ($type) {
				case 'int':
					return '(0|[1-9]\d*)';
					break;
				case 'float':
					return '((?:0|[1-9]\d*)(?:\.\d+)?f?)';
					break;
				case 'string':
					return '(\w+)';
					break;
				default:
					return $type::$regular;
			}
		}, preg_quote($kapafaa, '/'));
		return array($params, '^' . str_replace('#', '@', $regular) . '$');
	}

	
	/**
	 * @param string $text
	 * @return \Framework\Kapafaa\Script[]
	 */
	public function parse($text)
	{
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class()));
		if (($scripts = $cache->load($text)) === NULL) {
			$scripts = array();
			foreach (preg_split('/$\R?^/m', $text) as $line) {
				$line = trim($line);
				switch ($line) {
					case '': break;
					case '(': $script = new Script; break;
					case ')': $scripts[] = $script; break;
					default:
						$script->addObject($this->parseLine($line, $this->classes));
				}
			}
			$cache->save($text, $scripts);
		}
		return $scripts;
	}
	
	
	/**
	 * @param string $line
	 * @param array $classes
	 * @return \Framework\Kapafaa\Object
	 */
	protected function parseLine($line, array $classes)
	{
		$result = array(NULL, array());
		foreach ($classes as $class => $data) {
			if (preg_match('/' . $data['regular'] . '/', $line, $matches)) {
				array_shift($matches);
				$result = $this->createClass($class, $matches);
				break;
			}
		}
		return $result;
	}
	
	
	/**
	 * @param string $class
	 * @param array $params
	 * @return \Framework\Kapafaa\Object
	 */
	protected function createClass($class, array $params)
	{
		if (strpos($class, '\\') === 0) {
			$class = substr($class, 1);
		}
		$data = $this->classes[$class];
		$args = array();
		while (count($data['params'])) {
			$type = array_shift($data['params']);
			switch ($type) {
				case 'int':
				case 'float':
				case 'string':
					$args[] = array_shift($params);
					break;
				default: //class
					list($object, ) = $this->parseLine(array_shift($params), $this->getImplementors($type));
					$args[] = $object;
			}
		}
		return Nette\Reflection\ClassType::from($class)->newInstanceArgs($args);
	}
	
	
	protected function getImplementors($class)
	{
		if (strpos($class, '\\') === 0) {
			$class = substr($class, 1);
		}
		if (!isset($this->implementors[$class])) {
			foreach ($this->classes as $key => $value) {
				if ($value['parent'] == $class) {
					$this->implementors[$class][$key] = $value;
				}
			}
		}
		return $this->implementors[$class];
	}
}
