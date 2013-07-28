<?php

namespace App;

use Nette,
	Framework,
	Grido,
	Grido\Components\Filters\Filter;



class AuditControl extends Framework\Application\UI\BaseControl
{
	/** @var \Framework\Mapi\MapiRequestFactory @inject */
	public $mapiRequestFactory;
	
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Model\AuditFactory @inject */
	public $auditFactory;
		
	
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	public function createComponentBazaar($name)
	{
		$link = $this->presenter->lazyLink('showViewTransaction');
		
		//request
		$request = $this->mapiRequestFactory->create('transactions', 'txs');
		
		//types
		$tmp = $this->game->bazaarTransactionTypes;
		$types = array_combine($tmp, $tmp);
		
		//grido
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Mapi\MapiDataSource($request))
				->setPrimaryKey('txId')
				->setDefaultSort(array('txId' => 'DESC'))
				->setPropertyAccessor(new \Framework\Mapi\MapiPropertyAccessor);
		
		$grido->addColumn('txId', 'ID')
				->setSortable();
		
		$grido->addColumn('userId', 'UID')
				->setSortable()
				->setFilter(Filter::TYPE_NUMBER);
		
		$grido->addColumn('name', 'Uživatel')
				->setSortable()
				->setFilter();
				
		$grido->addColumn('type', 'Typ')
				->setSortable();

		$grido->addColumn('timestamp', 'Čas')
				->setSortable()
				->setCustomRender(function ($item) {
					return date('d.m.Y H:i:s', $item->timestamp);
				});
		
		$grido->addColumn('node', 'Data')
				->setTruncate(80)
				->setFilter();
		
		$grido->addFilterCustom('type', new \Framework\Forms\Controls\CheckList('Typ', $types))
				->setCondition(Grido\Components\Filters\Filter::CONDITION_CALLBACK, function ($item) {
					return array('type IN %i', $item);
				});
		
		$grido->addAction('show', 'Podrobnosti')
				->setIcon('list')
				->setCustomHref(function ($item) use ($link) {
					return $link->setParameter('id', $item->txId);
				});
		
		return $grido;
	}
	
	
	
	public function renderShowTransaction()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/transaction.latte');
		
		$id = $this->presenter->getParameter('id');
		$transactions = array_filter($this->mapiRequestFactory->create('transactions', 'txs')->load(), function ($item) use ($id) {
			return $item->txId == $id;
		});
		$transaction = array_pop($transactions);
		$template->transaction = $transaction;
		
		$template->render();
	}
	
	
	
	public function renderAudit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/audit.latte');
		$template->render();
	}
	
	
	
	public function createComponentAudit($name)
	{
		$link = $this->presenter->lazyLink('showViewAudit');
		
		//types
		$tmp = $this->game->auditEventTypes;
		$types = array_combine($tmp, $tmp);
		
		//grido
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->auditFactory->access()->audits)
				->setPrimaryKey('audit_event_id')
				->setDefaultSort(array('audit_event_id' => 'DESC'));
		
		$grido->addColumn('audit_event_id', 'ID')
				->setSortable();
		
		$grido->addColumn('user_id', 'UID')
				->setSortable()
				->setFilter(Filter::TYPE_NUMBER);
		
		$grido->addColumn('type', 'Typ')
				->setSortable()/*
				->setFilter(Filter::TYPE_SELECT, $types)*/;

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
		
		$grido->addFilterCustom('type', new \Framework\Forms\Controls\CheckList('Typ', $types))
				->setCondition(Grido\Components\Filters\Filter::CONDITION_CALLBACK, function ($item) {
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
		
		$id = $this->presenter->getParameter('id');
		$event = $this->auditFactory->access()->audits->where('audit_event_id = ?', $id)->fetch()->toArray();
		$event['data'] = json_decode($event['data']);
		
		$template->event = $event;
		
		$template->render();
	}
}
