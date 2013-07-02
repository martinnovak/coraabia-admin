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
		
		$grido->addColumn('userId', 'ID uživatele')
				->setSortable();
		
		$grido->addColumn('name', 'Uživatele')
				->setSortable();
				
		$grido->addColumn('type', 'Typ')
				->setSortable();

		$grido->addColumn('timestamp', 'Čas')
				->setSortable();
		
		$grido->addColumn('node', 'Data')
				->setSortable();
		
		return $grido;
	}
}
