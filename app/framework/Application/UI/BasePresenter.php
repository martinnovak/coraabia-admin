<?php

namespace Framework\Application\UI;

use Nette;



/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var \Nette\Localization\ITranslator @inject */
	public $translator;
	
	/** @var \Framework\Hooks\HookManager @inject */
	public $hookManager;
	
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
			$class = 'App\\' . ucfirst($name) . 'Control';
			$component = $this->context->createInstance($class);
		}
		
		$this->context->callInjects($component); //enable injecting into components
		return $component;
	}
}
