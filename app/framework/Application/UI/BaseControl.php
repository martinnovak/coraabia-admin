<?php

namespace Framework\Application\UI;

use Nette;


/**
 * Base control.
 */
abstract class BaseControl extends Nette\Application\UI\Control
{
	/** @var \Nette\Localization\ITranslator @inject */
	public $translator;
	
	/** @var \Framework\Hooks\HookManager @inject */
	public $hookManager;
	
	/** @var \Framework\Application\FormFactory @inject */
	public $formFactory;
	
	/** @var \Model\Locales @inject */
	public $locales;
	
	
	/**
	 * @param string|NULL $class
	 * @return \Nette\Templating\ITemplate 
	 */
	public function createTemplate($class = NULL) //intentionally public
	{
		$template = parent::createTemplate($class);
		$template->setTranslator($this->translator);
		$template->hookManager = $this->hookManager;
		$template->locales = $this->locales;
		return $template;
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$component = parent::createComponent($name);
		if (!$component) {
			$class = 'App\\' . ucfirst($this->locales->module) . 'Module\\' . ucfirst($name) . 'Control';
			if (!class_exists($class)) { //@todo THIS IS UGLY
				$class = 'App\\' . ucfirst($name) . 'Control';
			}
			
			$component = $this->getPresenter()->context->createInstance($class);
		}
		
		$this->getPresenter()->context->callInjects($component); //enable injecting into components
		return $component;
	}
}
