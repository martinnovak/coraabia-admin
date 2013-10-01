<?php

namespace Framework\Diagnostics;

use Nette,
	Model;


class LocalesPanel extends Panel
{
	const ICO = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAnhJREFUeNp0U21Ik1EUft7cbAQprajMfgQauWaYOoclVCYJDZTMaEGomWFs9EWSEn2IIIgGff0wkCIyggJJ6Id9mKwiJeZHGtOpc5TYDCGXs7LpspP3jPdFdD5wuOec5znn3nPv+0pEhFCorq2nQCDAvlqtRpk1XwqlU80PKm/d426XzxZJfr8f5eeLOV9xvW4RL9dI8gkqb9ZRmjEZ2+O3ovp2sMByIo/XO3cf8lp2phjdjj70u9ywFBwONhENDuSdJFtbJ3l9U/Trj59NYHb2L5uAnBcaoc09ZhHp4AhhYcuQoNehsbkVxvhYbFwXiZGJn/gx6edNVkVosFIThq9jPtgdQ8jZl4ZFIxwqtNK18lL2hzyTaLG14HlTE8f7TSZkpGcgNjqC4wsVNWi4X8sjqC5W3qCZmQCioqLxe2oao+M+OAe/oKujHT1tzVyQbS5knv5twobVkeyXXK2h8HA1cO5SFcl48dpGrZ19lJtvoYUQOcEJjQxRq1LNzd8/PMY7TUz4sHmLDkth/VotRj4PKnpRKyWkZdL03JvHxsTgyMFsaNdo4f3uRWPTKzQ8qA3eT4EVOaZMhXv89BmG3G4s12ig6n7/ki8jy3yctiUmwW5vR/quVKVQQBSn7kiB7d0HGI0p3MDZ8VZSXkGXsoeePKqHNmIFvJNT+NjViZ1GA3ocvdwgIV6PNnsHEpOSFY35aD6c7W8k5VPud/YiTqfHiOcbCweHR5FkMDLnHHBxTnBAFGsViBMIizPsppIrVdQzMEx7s8xUdKqU3J5xNuGLnOCERmjlOqWB3EQmi0+X0SeXh034C3nZpKV+Z3Ev82MxbyjdfwEGACMrj5F1Og1oAAAAAElFTkSuQmCC';
	
	/** @var \Model\Locales */
	protected $locales;
	
	
	/**
	 * @param \Nette\Latte\Engine $latte
	 * @param \Nette\Localization\ITranslator $translator 
	 * @param \Model\Locales $locales
	 */
	public function __construct(Nette\Latte\Engine $latte, Nette\Localization\ITranslator $translator, Model\Locales $locales)
	{
		parent::__construct($latte, $translator);
		$this->locales = $locales;
	}
	
	
	/**
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Locales"><img src="' . self::ICO . '" alt="icon" />' . strtoupper($this->locales->module) . '|' . strtoupper($this->locales->server) . '|' . strtoupper($this->locales->lang) . '</span>';
	}
	
	
	/**
	 * @return \Nette\Templating\ITemplate 
	 */
	public function getPanel()
	{
		$template = $this->createTemplate()
				->setFile(__DIR__ . '/templates/localesPanel.latte');
		$template->locales = $this->locales;
		return $template;
	}
}
