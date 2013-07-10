<?php

namespace Framework\Dibi\Bridges\Nette;

use Nette;



class DibiNette21Extension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig();
		
		foreach ($config as $name => $conf) {
			$useProfiler = isset($conf['profiler'])
				? $conf['profiler']
				: !$container->parameters['productionMode'];

			unset($conf['profiler']);

			if (isset($conf['flags'])) {
				$flags = 0;
				foreach ((array) $conf['flags'] as $flag) {
					$flags |= constant($flag);
				}
				$conf['flags'] = $flags;
			}

			$connection = $container->addDefinition($this->prefix($name))
				->setClass('DibiConnection', array($conf));

			if ($useProfiler) {
				$panel = $container->addDefinition($this->prefix($name . 'Panel'))
					->setClass('DibiNettePanel')
					->addSetup('Nette\Diagnostics\Debugger::getBar()->addPanel(?)', array('@self'))
					->addSetup('Nette\Diagnostics\Debugger::getBlueScreen()->addPanel(?)', array('DibiNettePanel::renderException'));

				$connection->addSetup('$service->onEvent[] = ?', array(array($panel, 'logEvent')));
			}
		}
	}

}
