<?php

namespace Framework\Application\UI;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var \Model\Editor @inject */
	public $editor;
	
	/** @var \Nette\Localization\ITranslator @inject */
	public $translator;
	
	/** @var \Framework\Hooks\HookManager @inject */
	public $hookManager;
	
	/** @var \Framework\Application\FormFactory @inject */
	public $formFactory;
	
	/** @var \Model\Locales @inject */
	public $locales;
	
	/** @var string @persistent */
	public $server;
	
	/** @var string @persistent */
	public $lang;
	

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
			
			$component = $this->context->createInstance($class);
		}
		
		$this->context->callInjects($component); //enable injecting into components
		return $component;
	}
	
	
	public function startup()
	{
		parent::startup();
		
		if ($this->getUser()->isLoggedIn()) {
			$params = array();
			
			if ($this->getUser()->getIdentity()->lang != $this->locales->lang) {
				$params['lang'] = $this->locales->lang;
			}
			if (!empty($this->locales->server) && $this->locales->server != $this->getUser()->getIdentity()->server) {
				$params['server'] = $this->locales->server;
			}
			if (!empty($this->locales->module) && $this->locales->module != $this->getUser()->getIdentity()->module) {
				$params['module'] = $this->locales->module;
			}
			
			if (!empty($params)) {
				try {
					$this->editor->updateUser($this->getUser()->getId(), $params);
				} catch (\Exception $e) {
					
				}
				
				if (isset($params['lang'])) {
					$this->getUser()->getIdentity()->lang = $params['lang'];
				}
				if (isset($params['server'])) {
					$this->getUser()->getIdentity()->server = $params['server'];
				}
				if (isset($params['module'])) {
					$this->getUser()->getIdentity()->module = $params['module'];
				}
			}
		}
	}
}
