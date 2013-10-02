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
	
	/** @var \Nette\Localization\ITranslator */
	private $translator;
	
	/** @var array */
	private $classes;
	
	/** @var array */
	private $implementors;
	
	
	/**
	 * @param \Nette\DI\Container $container
	 * @param \Nette\Caching\IStorage $storage
	 * @param \Nette\Localization\ITranslator $translator
	 */
	public function __construct(Nette\DI\Container $container, Nette\Caching\IStorage $storage, Nette\Localization\ITranslator $translator)
	{
		$this->container = $container;
		$this->storage = $storage;
		$this->translator = $translator;
	}
	
	
	/**
	 * @todo Get rid of container and inject RobotLoader directly somehow.
	 * @param boolean $rebuild
	 * @return \Framework\Kapafaa\KapafaaParser
	 */
	public function loadClassData($rebuild = FALSE)
	{
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
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
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		return $this;
	}
	
	
	/**
	 * @param array $indexed
	 * @return array
	 */
	protected function build(array $indexed)
	{
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		$classes = array();
		foreach ($indexed as $name => $file) {
			$rc = Nette\Reflection\ClassType::from($name);
			if (!$rc->hasAnnotation('kapafaa') || !$rc->isSubclassOf('\Framework\Kapafaa\Object')) {
				continue;
			}
			
			$kapafaa = $rc->getAnnotation('kapafaa');
			$description = $rc->getAnnotation('description');
			list($regular, $paramTypes) = $this->parseRegular($rc, $kapafaa);
			$parent = $rc->getParentClass();
			
			$def = array(
				'name' => $this->translator->translate($description ?: str_replace('#', '@', $kapafaa)),
				'type' => ltrim($name, '\\'),
				'kapafaa' => str_replace('#', '@', $kapafaa),
				'regular' => $regular,
				'parent' => ltrim($parent ? $parent->getName() : '', '\\'),
				'file' => $file
			);
			
			preg_match_all('/%([a-z]+)%/i', $def['kapafaa'], $matches);
			$params = array();
			for ($i = 0; $i < count($matches[1]); $i++) {
				$params[] = array(
					'name' => $matches[1][$i],
					'type' => ltrim($paramTypes[$i], '\\'),
					'value' => NULL
				);
			}
			$def['params'] = $params;
			$classes[] = $def;
		}
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		return $classes;
	}
	
	
	/**
	 * @param \Nette\Reflection\ClassType $rc
	 * @param string $kapafaa
	 * @return array
	 * @throws \Framework\Kapafaa\KapafaaException
	 */
	protected function parseRegular(Nette\Reflection\ClassType $rc, $kapafaa)
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
				case ':negation':
					return '(\!?)';
					break;
				default:
					return $type::$regular;
			}
		}, preg_quote($kapafaa, '/'));
		return array('^' . str_replace('#', '@', $regular) . '$', $params);
	}

	
	/**
	 * @param string $text
	 * @return \Framework\Kapafaa\Script[]
	 */
	public function parse($text)
	{
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
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
		\Framework\Diagnostics\TimerPanel::timer(__METHOD__);
		return $scripts;
	}
	
	
	/**
	 * @param string $line
	 * @param array $classes
	 * @return \Framework\Kapafaa\Object
	 */
	protected function parseLine($line, array $classes)
	{
		$result = NULL;
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
		$data = $this->classes[$class];
		if (strpos($data['type'], '\\') === 0) {
			$data['type'] = substr($data['type'], 1);
		}
		$args = array();
		while (count($data['params'])) {
			$type = array_shift($data['params']);
			switch ($type['type']) {
				case 'int':
				case 'float':
				case 'string':
					$args[] = array_shift($params);
					break;
				case ':negation':
					$args[] = array_shift($params) ? new TruthValues\Negative() : new TruthValues\Positive();
					break;
				default: //class
					$args[] = $this->parseLine(array_shift($params), $this->getImplementors($type['type']));
			}
		}
		return Nette\Reflection\ClassType::from($data['type'])->newInstanceArgs($args);
	}
	
	
	/**
	 * @param string $class
	 * @return array
	 */
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
