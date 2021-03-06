<?php

namespace App\GameModule;

use Framework,
	Nette,
	Coraabia;


/**
 * @method setConnectionId(string)
 */
class ConnectionControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Framework\Kapafaa\KapafaaParser @inject */
	public $kapafaaParser;
	
	/** @var string */
	private $connectionId;
	
	
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
		$editLink = $this->getPresenter()->lazyLink('editConnection');
		$readyLink = $this->lazyLink('readyConnection');
		$removeLink = $this->lazyLink('deleteConnection');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->game->getConnections()))
				->setPrimaryKey('connection_id')
				->setDefaultSort(array('connection_id' => 'ASC'));
		
		$grido->addColumnText('connection_id', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->connection_id))
							->setText($item->connection_id);
				});
		
		$grido->addColumnText('name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->connection_id))
							->setText($self->translator->translate('connection-name.' . $item->connection_id . '.' . $item->version));
				});
				
		$versions = $this->game->getAllConnectionsVersions();
		$grido->addColumnNumber('version', 'V')
				->setCustomRender(function ($item) use ($self, $versions) {
					return implode(' | ', array(
						'<span class="' . \Coraabia\ServerEnum::DEV . '">' . (isset($versions[$item->connection_id][\Coraabia\ServerEnum::DEV]) ? $versions[$item->connection_id][\Coraabia\ServerEnum::DEV] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::STAGE . '">' . (isset($versions[$item->connection_id][\Coraabia\ServerEnum::STAGE]) ? $versions[$item->connection_id][\Coraabia\ServerEnum::STAGE] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::BETA . '">' . (isset($versions[$item->connection_id][\Coraabia\ServerEnum::BETA]) ? $versions[$item->connection_id][\Coraabia\ServerEnum::BETA] : '&times;') . '</span>'
					));
				});
		
		if ($this->locales->server == Coraabia\ServerEnum::DEV || $this->locales->server == Coraabia\ServerEnum::STAGE) {
			$grido->addAction('ready', strtoupper($this->locales->server == Coraabia\ServerEnum::DEV ? Coraabia\ServerEnum::STAGE : Coraabia\ServerEnum::BETA))
					->setIcon('share')
					->setCustomHref(function ($item) use ($self, $readyLink) {
						return $readyLink->setParameter('connId', $item->connection_id)->setParameter('srv', $self->locales->server == Coraabia\ServerEnum::DEV ? Coraabia\ServerEnum::STAGE : Coraabia\ServerEnum::BETA);
					})
					->setConfirm(function ($item) {
						return 'Jste si jisti?';
					});
		}
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->connection_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat konexi '" . $self->translator->translate('connection-name.' . $item->connection_id . '.' . $item->version) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteConnection()
	{
		$connectionId = $this->getParameter('id');
		try {
			$this->game->deleteConnection($connectionId);
			$this->getPresenter()->flashMessage('Konexe byla smazána.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function renderCreate()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/connectionForm.latte');
		$template->render();
	}
	
	
	public function createComponentConnectionForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Nastavení');
		
		$form->addText('connection_id', 'ID')
				->setRequired('Konexe musí mít ID.');
		
		$form->addText('version', 'Verze')
				->setDisabled()
				->setOmitted();
		
		$form->addText('influence_cost', 'Vliv')
				->setRequired();
		
		$types = $this->game->getConnectionTypes();
		$form->addSelect('type', 'Typ', array_combine($types, $types))
				->setRequired();
		
		$form->addText('art_id', 'Art ID');
		
		$form->addGroup('Efekt');
		
		$form->addTextArea('effect_data', 'Efekt');
		
		foreach ($this->locales->getLangs() as $lang) {
			$form->addGroup(strtoupper($lang));
			
			$form->addText('connection_name_' . $lang, 'Jméno');
			
			$form->addTextArea('connection_description_' . $lang, 'Popis');
			
			$form->addTextArea('connection_tooltip_' . $lang, 'Tooltip');
		}
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		if ($this->connectionId !== NULL) {
			try {
				$connection = $this->game->getConnectionById($this->connectionId);
				$form->setDefaults($connection);
				$form->setDefaults($this->getConnectionTexts($this->connectionId, $connection->version));
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
			}
		}
		
		$form->onSuccess[] = $this->connectionFormSuccess;
		
		return $form;
	}
	
	
	protected function getConnectionTexts($connectionId, $version)
	{
		$result = array();
		foreach ($this->locales->langs as $lang) {
			$result['connection_name_' . $lang] = $this->translator->getTranslation('connection-name.' . $connectionId . '.' . $version, $lang);
			$result['connection_description_' . $lang] = $this->translator->getTranslation('connection-description.' . $connectionId . '.' . $version, $lang);
			$result['connection_tooltip_' . $lang] = $this->translator->getTranslation('connection-tooltip.' . $connectionId . '.' . $version, $lang);
		}
		return $result;
	}
	
	
	public function connectionFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$connection = NULL;
		
		try {
			$this->kapafaaParser->parse($values->effect_data); //sanity check
			$connection = $this->game->saveConnection($values);
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($this->translator instanceof \Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
		
		if ($connection) {
			$this->getPresenter()->redirect('Connection:editConnection', array('id' => $connection->connection_id));
		}
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		
		$template->connectionId = $this->connectionId;
		
		$template->render();
	}
	
	
	public function handleReadyConnection()
	{
		$connectionId = $this->getParameter('connId');
		$server = $this->getParameter('srv');
		$follow = $this->getParameter('follow');
		$saved = FALSE;
		try {
			$this->game->readyConnection($connectionId, $server);
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
			$this->getPresenter()->redirect('Connection:editConnection', array('id' => $connectionId, 'server' => $server));
		}
	}
}
