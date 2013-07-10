<?php

namespace App;

use Nette,
	Framework;



class ActivityControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	
	
	public function renderCreate()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/create.latte');
		$template->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate()->setFile(__DIR__ . '/createScripts.latte');
			$tmpl->gamerooms = $self->game->gamerooms->fetchAll();
			$hook->addTemplate($tmpl);
		});
		$template->render();
	}
}
