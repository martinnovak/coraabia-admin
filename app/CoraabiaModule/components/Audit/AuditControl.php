<?php

namespace App\CoraabiaModule;

use Framework,
	Grido\Components\Filters\Filter;


class AuditControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Model\Audit @inject */
	public $audit;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
		
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentBazaar($name)
	{
		$link = $this->getPresenter()->lazyLink('showViewTransaction');
		
		//types
		$tmp = $this->bazaar->getBazaarTransactionTypes();
		$types = array_combine($tmp, $tmp);
		
		//grido
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\MapiDataSource($this->bazaar->getTransactions()))
				->setPrimaryKey('txId')
				->setDefaultSort(array('txId' => 'DESC'))
				->setPropertyAccessor(new Framework\Grido\PropertyAccessors\MapiPropertyAccessor);
		
		$grido->addColumn('txId', 'ID')
				->setSortable();
		
		$grido->addColumn('userId', 'UID')
				->setSortable()
				->setCustomRender(function ($item) {
					return isset($item->userId) ? $item->userId : '';
				})
				->setFilter(Filter::TYPE_NUMBER);
		
		$grido->addColumn('name', 'Uživatel')
				->setSortable()
				->setCustomRender(function ($item) {
					return isset($item->name) ? $item->name : '';
				})
				->setFilter();
				
		$grido->addColumn('type', 'Typ')
				->setSortable();

		$grido->addColumn('timestamp', 'Čas')
				->setSortable()
				->setCustomRender(function ($item) {
					return date('d.m.Y H:i:s', $item->timestamp / 1000);
				});
		
		$grido->addColumn('node', 'Data')
				->setTruncate(80)
				->setFilter();
		
		//@todo
		/*$grido->addFilterCustom('type', new Framework\Forms\Controls\MultiOptionList('Typ', $types))
				->setCondition(Filter::CONDITION_CALLBACK, function ($item) {
					return array('type IN %i', $item);
				});
		
		$grido->addAction('show', 'Podrobnosti')
				->setIcon('list')
				->setCustomHref(function ($item) use ($link) {
					return $link->setParameter('id', $item->txId);
				});*/
		
		return $grido;
	}
	
	
	public function renderShowTransaction()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/transaction.latte');
		
		$template->transaction = $this->bazaar->getTransactionById($this->getPresenter()->getParameter('id'));
		
		$template->render();
	}
	
	
	public function renderAudit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/audit.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentAudit($name)
	{
		$link = $this->getPresenter()->lazyLink('showViewAudit');
		
		//types
		$tmp = $this->audit->getAuditEventTypes();
		$types = array_combine($tmp, $tmp);
		
		//grido
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->audit->getAudits())
				->setPrimaryKey('audit_event_id')
				->setDefaultSort(array('audit_event_id' => 'DESC'));
		
		$grido->addColumn('audit_event_id', 'ID')
				->setSortable();
		
		$grido->addColumn('user_id', 'UID')
				->setSortable()
				->setFilter(Filter::TYPE_NUMBER);
		
		$grido->addColumn('type', 'Typ')
				->setSortable();

		$grido->addColumn('timestamp', 'Čas')
				->setSortable()
				->setCustomRender(function ($item) {
					return date('d.m.Y H:i:s', strtotime($item->timestamp));
				});
		
		$grido->addColumn('search1', 'Search 1')
				->setSortable()
				->setFilter();
		
		$grido->addColumn('search2', 'Search 2')
				->setSortable()
				->setFilter();
		
		$grido->addColumn('data', 'Data')
				->setTruncate(80)
				->setFilter();
		
		$grido->addFilterCustom('type', new Framework\Forms\Controls\MultiOptionList('Typ', $types))
				->setCondition(Filter::CONDITION_CALLBACK, function ($item) {
					return array('type IN %i', $item);
				});
		
		$grido->addAction('show', 'Podrobnosti')
				->setIcon('list')
				->setCustomHref(function ($item) use ($link) {
					return $link->setParameter('id', $item->audit_event_id);
				});
		
		return $grido;
	}
	
	
	public function renderShowAudit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/event.latte');
		
		$id = $this->getPresenter()->getParameter('id');
		
		$event = $this->audit->getEventById($id);
		$event['data'] = json_decode($event['data']);
		
		$template->event = $event;
		
		$template->render();
	}
}
