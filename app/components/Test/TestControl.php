<?php

namespace App;

use Nette,
	Framework;



class TestControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\API @inject */
	public $api;
	
	/** @var \Model\Transaction\ITransactionFactory @inject */
	public $transactionFactory;
	
	
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/test.latte');
		
		$result = $this->api->query(array(
			'id' => 'transactions',
			//'start' => 0,
			//'count' => 50,
			//'type' => 'SAVE_ITEM',
			//'userId' => 126,
			//'from' => $this->api->formatDate('2010-09-10 12:43:42'),
			//'to' => $this->api->formatDate('2010-09-10T14:43:42')
		));
		
		$transactions = array();
		$keys = array();
		foreach ($result->txs as $tData) {
			$tmp = $this->transactionFactory->create();
			$tData->node = json_encode($tData->node);
			$tmp->data = $tData;
			$tmp = $tmp->toArray();
			$keys = array_unique(array_merge($keys, array_keys($tmp)));
			$transactions[] = $tmp;
		}
		
		$template->keys = $keys;
		$template->test = $transactions;
		$template->result = '';//json_encode($result);
		
		$template->render();
	}
}
