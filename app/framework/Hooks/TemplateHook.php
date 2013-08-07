<?php

namespace Framework\Hooks;

use Nette\Templating\ITemplate;


class TemplateHook extends BaseHook
{
	/** @var array \Nette\Templating\ITemplate */
	protected $templates = array();
	
	
	/**
	 * @param \Nette\Templating\ITemplate $template 
	 */
	public function addTemplate(ITemplate $template) {
		$this->templates[] = $template;
	}
	
	
	/**
	 * Called from template.
	 */
	public function render() {
		foreach ($this->templates as $template) {
			$template->render();
		}
	}
}
