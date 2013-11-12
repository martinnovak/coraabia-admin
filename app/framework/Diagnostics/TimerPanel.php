<?php

namespace Framework\Diagnostics;

use Nette;


class TimerPanel extends Panel
{
	const ICO = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAHpSURBVHjajJOxa1NRFMZ/ea+DNNAkpIQYooOJi4gQCBmDuGVUyJ5FsvhnFNx1Emf7wCnQpItIklZp1SwxNTpEoo90CBqMSWnq67vvOHgfPNNS+sGFew/n+75z7zkXzuIm8BjYB46AY6ALPAFyXAAT2AD+AAI4wGfgkxYSvZ4BkWXyCvBCJ0wsy9pyXdcWEURElFI/Op3OTjabtXVOE1gNCmwAEo/H3zuOM5R/UMBmu91uiR9QalKpVN5pEcsn3wBOgZ/aVUREHMcZAlIoFL7I/1jkcrmBFrlrAA+BFcuy9kzTvLZ8t7W1tdOl0JVarTbR+0cAH4Bj13UPgzZ+BaVSqbtUgXieN43FYlPgmwHcAr6bppk6pzOq3++vNxqNVr1ebw8Gg32AUCgUKRaLQ+AqwAI4WHZxXfcQmAXaJ6lUaiwiJyIi5XK5AzghoAdcV0o5hmGsB+3n83l3Npsd+edoNBoJh8O3RWRmGMYCcAGeAtJsNrflkhiNRm90VdsAdwAPGHqeN7kEXyUSCX8W7vvVPQckn883Pc/7fQHZqVardU1uAYYvENHjKcBBr9d7pZQaB9r2y7btnWQyuadzPgJnurYKbAZefZpOp99mMpldYByIvz6PHMQ94CXwFTjRv9MGtoAHQCiY/HcA4TCdApPucwYAAAAASUVORK5CYII=';
	
	/** @var array */
	private static $timers = array();
	
	/** @var array */
	private static $running = array();
	
	/** @var float */
	private static $max;
	
	
	/**
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Časy"><img src="' . self::ICO . '" alt="icon" />' . (count(self::$timers) ? round((float)self::$max - (float)Nette\Diagnostics\Debugger::$time, 3) : 0) . ' s</span>';
	}
	
	
	/**
	 * @return \Nette\Templating\ITemplate 
	 */
	public function getPanel()
	{
		$template = $this->createTemplate()
				->setFile(__DIR__ . '/templates/timerPanel.latte');
		$template->timers = self::$timers;
		$template->min = Nette\Diagnostics\Debugger::$time;
		$template->max = self::$max;
		return $template;
	}
	
	
	/**
	 * @param string $name
	 */
	public static function timer($name)
	{
		$time = microtime(TRUE);
		$memory = memory_get_usage(TRUE);
		if (!isset(self::$running[$name])) {
			self::$running[$name] = TRUE;
			self::$timers[$name][] = array('time' => array($time, 0), 'memory' => array($memory, 0));
		} else {
			self::$timers[$name][count(self::$timers[$name]) - 1]['time'][1] = $time;
			self::$timers[$name][count(self::$timers[$name]) - 1]['memory'][1] = $memory;
			if ((float)self::$max < $time) {
				self::$max = $time;
			}
			unset(self::$running[$name]);
		}
	}
}
