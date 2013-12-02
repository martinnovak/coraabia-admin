<?php

namespace App\GameModule;

use Framework,
	Nette,
	Coraabia;


/**
 * @method setGameroomId(string)
 */
class GameroomControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var string */
	private $gameroomId;
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	

	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentList($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editGameroom');
		$readyLink = $this->lazyLink('readyGameroom');
		$removeLink = $this->lazyLink('deleteGameroom');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->game->getGamerooms()))
				->setPrimaryKey('gameroom_id')
				->setDefaultSort(array('gameroom_id' => 'ASC'));
		
		$grido->addColumnText('gameroom_id', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->gameroom_id))
							->setText($item->gameroom_id);
				});
		
		$grido->addColumnText('name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->gameroom_id))
							->setText($self->translator->translate('gameroom-name.' . $item->gameroom_id . '.' . $item->version));
				});
				
		$versions = $this->game->getAllGameroomsVersions();
		$grido->addColumnNumber('version', 'V')
				->setCustomRender(function ($item) use ($self, $versions) {
					return implode(' | ', array(
						'<span class="' . \Coraabia\ServerEnum::DEV . '">' . (isset($versions[$item->gameroom_id][\Coraabia\ServerEnum::DEV]) ? $versions[$item->gameroom_id][\Coraabia\ServerEnum::DEV] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::STAGE . '">' . (isset($versions[$item->gameroom_id][\Coraabia\ServerEnum::STAGE]) ? $versions[$item->gameroom_id][\Coraabia\ServerEnum::STAGE] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::BETA . '">' . (isset($versions[$item->gameroom_id][\Coraabia\ServerEnum::BETA]) ? $versions[$item->gameroom_id][\Coraabia\ServerEnum::BETA] : '&times;') . '</span>'
					));
				});
		
		if ($this->locales->server == Coraabia\ServerEnum::DEV || $this->locales->server == Coraabia\ServerEnum::STAGE) {
			$grido->addAction('ready', strtoupper($this->locales->server == Coraabia\ServerEnum::DEV ? Coraabia\ServerEnum::STAGE : Coraabia\ServerEnum::BETA))
					->setIcon('share')
					->setCustomHref(function ($item) use ($self, $readyLink) {
						return $readyLink->setParameter('gammId', $item->gameroom_id)->setParameter('srv', $self->locales->server == Coraabia\ServerEnum::DEV ? Coraabia\ServerEnum::STAGE : Coraabia\ServerEnum::BETA);
					})
					->setConfirm(function ($item) {
						return 'Jste si jisti?';
					});
		}
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->gameroom_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat gameroom '" . $self->translator->translate('gameroom-name.' . $item->gameroom_id . '.' . $item->version) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteGameroom()
	{
		$gameroomId = $this->getParameter('id');
		try {
			$this->game->deleteGameroom($gameroomId);
			$this->getPresenter()->flashMessage('Gameroom byl smazán.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function renderCreate()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/gameroomForm.latte');
		$template->render();
	}
	
	
	public function createComponentGameroomForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Nastavení');
		
		$form->addText('gameroom_id', 'ID')
				->setRequired('Gameroom musí mít ID.');
		
		$form->addText('version', 'Verze')
				->setDisabled()
				->setOmitted();
		
		$form->addGroup('@todo');
		
		foreach ($this->locales->getLangs() as $lang) {
			$form->addGroup(strtoupper($lang));
			
			$form->addText('gameroom_name_' . $lang, 'Jméno');
		}
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		if ($this->gameroomId !== NULL) {
			try {
				$gameroom = $this->game->getGameroomById($this->gameroomId);
				$form->setDefaults($gameroom);
				$form->setDefaults($this->getGameroomTexts($this->gameroomId, $gameroom->version));
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
			}
		}
		
		$form->onSuccess[] = $this->gameroomFormSuccess;
		
		return $form;
	}
	
	
	protected function getGameroomTexts($gameroomId, $version)
	{
		$result = array();
		foreach ($this->locales->langs as $lang) {
			$result['gameroom_name_' . $lang] = $this->translator->getTranslation('gameroom-name.' . $gameroomId . '.' . $version, $lang);
		}
		return $result;
	}
	
	
	public function gameroomFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$gameroom = NULL;
		
		try {
			$gameroom = $this->game->saveGameroom($values);
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($this->translator instanceof \Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
		
		if ($gameroom) {
			$this->getPresenter()->redirect('Gameroom:editGameroom', array('id' => $gameroom->gameroom_id));
		}
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		
		$template->gameroomId = $this->gameroomId;
		
		$template->render();
	}
	
	
	public function handleReadyGameroom()
	{
		$gameroomId = $this->getParameter('gammId');
		$server = $this->getParameter('srv');
		$follow = $this->getParameter('follow');
		$saved = FALSE;
		try {
			$this->game->readyGameroom($gameroomId, $server);
			$saved = TRUE;
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		if ($this->translator instanceof \Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
		
		if (!$saved || !$follow) {
			$this->redirect('this');
		} else {
			$this->getPresenter()->redirect('Gameroom:editGameroom', array('id' => $gameroomId, 'server' => $server));
		}
	}
}
