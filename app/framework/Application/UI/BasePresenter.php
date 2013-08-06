<?php

namespace Framework\Application\UI;

use Nette,
	App;



/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
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
			$class = 'App\\' . ucfirst($name) . 'Control';
			$component = $this->context->createInstance($class);
		}
		
		$this->context->callInjects($component); //enable injecting into components
		return $component;
	}
	
	
	
	public function startup()
	{
		parent::startup();
		
		if ($this->user->isLoggedIn()) {
			$lang = $this->user->getIdentity()->data['lang'];
			if ($lang != $this->lang) { //intentionaly !=
				$this->redirect('this', array('lang' => $lang));
			}
		}
	}
}
