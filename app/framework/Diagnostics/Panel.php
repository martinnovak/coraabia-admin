<?php

namespace Framework\Diagnostics;

use Nette;


/**
 * @method \Nette\Latte\Engine getLatte()
 * @method \Nette\Localization\ITranslator getTranslator()
 */
abstract class Panel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	/** @var \Nette\Latte\Engine */
	private $latte;
	
	/** @var \Nette\Localization\ITranslator */
	private $translator;
	

	/**
	 * @param \Nette\Latte\Engine $latte
	 * @param \Nette\Localization\ITranslator $translator 
	 */
	public function __construct(Nette\Latte\Engine $latte, Nette\Localization\ITranslator $translator)
	{
		$this->latte = $latte;
		$this->translator = $translator;
	}
	
	
	/**
	 * @param  string|NULL
	 * @return \Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL)
	{
		$template = $class ? new $class : new Nette\Templating\FileTemplate;
		$template->registerFilter($this->latte);
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		$template->setTranslator($this->translator);

		return $template;
	}
}
