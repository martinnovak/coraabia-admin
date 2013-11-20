<?php

namespace App\GameModule;

use Framework,
	Nette;


/**
 * @method setConnectionId(string)
 */
class ConnectionControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
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
		$removeLink = $this->lazyLink('deleteConnection');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->getConnections())
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
							->setText($self->translator->translate('connection.' . $item->connection_id . '.' . $item->version));
				});
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->connection_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat konexi '" . $self->translator->translate('connection.' . $item->connection_id . '.' . $item->version) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteConnection()
	{
		$connectionId = $this->getParameter('id');
		try {
			$connections = $this->game->getConnections();
			$this->game->deleteConnection($connectionId, $connections[$connectionId]->version);
			$this->getPresenter()->flashMessage('Konexe byla smazána.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
}
