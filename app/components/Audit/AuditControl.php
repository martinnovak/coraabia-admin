<?php

namespace App;

use Nette,
	Framework,
	Grido;



class AuditControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\API @inject */
	public $api;
	
	/** @var \Model\Transaction\ITransactionFactory @inject */
	public $transactionFactory;
	
	
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	public function createComponentBazaar($name)
	{
		$self = $this;
		
		$transactions = array();
		foreach ($this->api->query(array('id' => 'transactions'))->txs as $tData) {
			$tData->node = json_encode($tData->node);
			$tmp = $this->transactionFactory->create();
			$tmp->data = $tData;
			$transactions[] = $tmp->toArray();
		}
		
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($transactions)
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('txId')
				->setDefaultSort(array('txId' => 'asc'));
		
		$grido->addColumn('txId', 'ID')
				->setSortable();
		
		$grido->addColumn('userId', 'UID')
				->setSortable();
		
		$grido->addColumn('name', 'Uživatel')
				->setSortable();
				
		$grido->addColumn('type', 'Typ')
				->setSortable();

		$grido->addColumn('timestamp', 'Čas')
				->setSortable()
				->setCustomRender(function ($item) {
					return date('d.m.Y H:i:s', $item['timestamp']);
				});
		
		$grido->addColumn('node', 'Data')
				->setSortable()
				->setTruncate(100);
		
		return $grido;
	}
}
