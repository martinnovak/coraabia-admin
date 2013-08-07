<?php

namespace Framework\Diagnostics;

use Nette,
	Model;


class LocalesPanel extends Panel
{
	const ICO = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC60lEQVR42qWTe0hTURzHv3fe+UJFTUyagaaCQiY+CnzVrkGISn+kzq1U1LayBxSUSgVlwag0CqHA3FIsSZvOwrCMqOurhPJFC7LU1HChqKRWm7o717lTbFL91YEfl8M553M+53vuofCfjbLtFBcXC8bHx9MpipKRbhSpjaQmSXVbLJZaX1/fejJn+a8AuVzuTz6awMCgKDHDICAwCC6ubpibm8fQ0CDaWlmMDA92kzkStVo9sg6Qn5/vZ7HgrSQjw0ssFq/RY2Oi0dbZBbMFMJN929pa0dRQOy0QCLaXl5ePWgG8tl6vfyORyiIZMQMBQWbcY/Agi0UMAbQTgKyGQbWMhYmHEJPmxtoekUi0gz8OpVAoUv23BDScKiiyLuaL17KsGrTbGPCARQ4oL7sK/dhwmkql0vKA5lz54aSIiEhk3mdQm8muBRMXu/4I2WT8VhqLnu5uNN2veEIAyTxAf0FZssnD3R12/O42BrviosF2rAA43sAMLBCDyelZ3L5S+JUARFReXp6p9KaaVmgScHc/C8FqtAnx0X/c+cPnXTikYXA5hcX1Mwe5yspKIZWTkzN5TnnD29PDxoCU/vMAcnNzUVVVtQZw8gmGkRjMfJuFSnlyqrq62puSSqX9WUcKwsLCI3BMy0At+Z3B1JcBeG4OtuovEf3jZFyZzGLgfS8e3Sl9V1dXF0alpqaWBIRsKzhw9CxcHGnQgpUMeIvl1fC41RvgIT9JCI0VSox90pVqtdpC/j+g+/r6ZlKyT7iFRsUSiD2KHjMo3ctag+QBfP9SEosfhiV87H+FpzVl8+Hh4RvIWs5qm56eHrewuMgm7FPQwZHxcHZyhL29EBef7cb5PS+wsGSCwbiAwd4OvGxUcY4ODkx9fX3nurcgkUh2GgyGFncvH6etMYnw8QuBk6s7jN9nMTH6AbrXLZibnjA6OzsnajSa9n+9Rlqn010zmUxSjuO8zGazHWlmmqanhUJhXWho6Gle23bNL7k2RSBGzG3UAAAAAElFTkSuQmCC';
	
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
		return '<span title="Locales"><img src="' . self::ICO . '" alt="icon" />' . strtoupper($this->locales->server) . '|' . $this->locales->lang . '</span>';
	}
	
	
	/**
	 * @return \Nette\Templating\ITemplate 
	 */
	public function getPanel()
	{
		$template = new Nette\Templating\FileTemplate(__DIR__ . '/templates/localesPanel.latte');
		$template->registerFilter($this->latte);
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		$template->setTranslator($this->translator);
		$template->locales = $this->locales;
		return $template;
		
	}
}
