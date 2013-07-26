<?php

namespace Framework\Application;

use Nette,
	Kdyby;



class FormFactory extends Nette\Object
{
	/** @var \Nette\Localization\ITranslator */
	private $translator;
	
	
	
	/**
	 * @param \Nette\Localization\ITranslator $translator 
	 */
	public function __construct(Nette\Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}
	
	
	
	/**
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param string|NULL $name
	 * @return \Nette\Application\UI\Form 
	 */
	public function create(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		$form = new Nette\Application\UI\Form($parent, $name);
		
		$form->setRenderer(new Kdyby\BootstrapFormRenderer\BootstrapRenderer);
		$form->setTranslator($this->translator);
		
		return $form;
	}
}
