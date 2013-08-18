<?php

namespace App\GameModule;

use Nette,
	Framework;


class TestPresenter extends Framework\Application\UI\SecuredPresenter
{
	public function renderTest()
	{
		$parser = new Framework\Xml\XmlParser();
		$parser->setFile(__DIR__ . '/../../../data/game.dev.xml')
			->parse();
		$this->template->parsed = $parser->parsed;
	}
}