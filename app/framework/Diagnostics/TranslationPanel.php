<?php

namespace Framework\Diagnostics;

use Nette;



class TranslationPanel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	const ICO = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAI9JREFUeNpi/P//PwMlgAVEMDIyvgNSF4A4mEh9s4DYGWi5ECPIBUADYM74RKQBfCACqJcRZsBvmGtIASADWJC8shVN3gKIhZFcdhhN3htmCoiqwWLBdpA0FJ/FIl8O1osnFvYiGXARhxcYmBgoBKMG0NiAn0js3/iSIzbhQiD+i5QOQLgZm15GSrMzQIABALI8PuRNwjeFAAAAAElFTkSuQmCC';
	
	/** @var \Nette\Latte\Engine */
	protected $latte;
	
	/** @var \Nette\Localization\ITranslator */
	protected $translator;
	
	

	/**
	 * @param \Nette\Latte\Engine $latte
	 * @param \Nette\Localization\ITranslator $translator 
	 */
	public function __construct(Nette\Latte\Engine $latte, Nette\Localization\ITranslator $translator)
	{
		$this->latte = $latte;
		$this->translator = $translator;
	}
	
	
	
	public function getTab()
	{
		return '<span title="Překlady"><img src="' . self::ICO . '" alt="icon" />rans</span>';
	}
	
	
	
	/**
	 * @return \Nette\Templating\ITemplate 
	 */
	public function getPanel()
	{
		$template = new Nette\Templating\FileTemplate(__DIR__ . '/templates/translationPanel.latte');
		$template->registerFilter($this->latte);
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		$template->setTranslator($this->translator);
		$template->translations = $this->translator->translations;
		return $template;
		
	}
}