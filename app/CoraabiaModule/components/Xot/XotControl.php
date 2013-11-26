<?php

namespace App\CoraabiaModule;

use Nette,
	Framework;


/**
 * @method setRefillId(int)
 */
class XotControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $refillId;


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
	public function createComponentRefillList($name)
	{
		$editLink = $this->getPresenter()->lazyLink('editRefill');
		$removeLink = $this->lazyLink('deleteRefill');
		$revalidateLink = $this->lazyLink('revalidateRefill');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->bazaar->getRefills()))
				->setPrimaryKey('refillId')
				->setDefaultSort(array('refillId' => 'DESC'));
		
		$grido->addColumn('refillId', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->refillId))
							->setText($item->refillId);
				});
		
		$currencies = $this->bazaar->getCurrencies();
		$grido->addColumn('amount', 'Množství')
				->setCustomRender(function ($item) use ($editLink, $currencies) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->refillId))
							->setText($item->amount . ' ' . $currencies[$item->currencyInt]);
				});
		
		$grido->addColumn('price', 'Cena')
				->setCustomRender(function ($item) {
					return $item->price . ' ' . $item->currencyExt;
				});
		
		$grido->addColumn('paymentService', 'Brána')
				->setCustomRender(function ($item) {
					return implode(', ', $item->paymentService);
				});
				
		$grido->addColumn('valid', '')
				->setCustomRender(function ($item) {
					return $item->valid ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addAction('revalidate', 'Povolit/Zakázat')
				->setIcon('refresh')
				->setCustomHref(function ($item) use ($revalidateLink) {
					return $revalidateLink->setParameter('id', $item->refillId);
				});
				
		/*$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->refillId);
				})
				->setConfirm(function ($item) {
					return "Opravdu chcete smazat nabídku '{$item->refillId}'?";
				});*/
				
		return $grido;
	}
	
	
	public function handleDeleteRefill()
	{
		$refillId = (int)$this->getParameter('id');
		try {
			$this->bazaar->deleteRefill($refillId);
			$this->getPresenter()->flashMessage('Nabídka byla smazána.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function handleRevalidateRefill()
	{
		$refillId = (int)$this->getParameter('id');
		try {
			$refill = $this->bazaar->getRefillById($refillId);
			if (!$refill) {
				throw new \Exception("Nabídka '$refillId' nebyla nalezena.");
			}
			
			$refill->valid = !$refill->valid;
			$this->bazaar->saveRefill((array)$refill);
			$this->getPresenter()->flashMessage("Nabídka '$refillId' byla " . ($refill->valid ? "povolena." : "zakázána."), 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function renderCreate()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/create.latte');
		$template->render();
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$template->render();
	}
	
	
	/**
	 * @todo
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentRefillForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Vytvořit');
		
		$form->onSuccess[] = $this->refillFormSuccess;
		
		return $form;
	}
	
	
	/**
	 * @todo
	 * @param \Nette\Application\UI\Form $form
	 */
	public function refillFormSuccess(Nette\Application\UI\Form $form)
	{
		/*$values = $form->getValues();
		$refillId = NULL;
		
		try {
			$refill = array(
				
			);
			
			$refillId = $this->bazaar->saveRefill($refill);
			$this->getPresenter()->flashMessage("Nabídka byla vytvořena.", 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		if ($refillId) {
			$this->getPresenter()->redirect('Xot:editRefill', array('id' => $refillId));
		}*/
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentRefillEditForm($name)
	{
		$form = $this->createComponentRefillForm($name);
		
		unset($form['submit']);
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		$form->onSuccess = array($this->refillEditFormSuccess);
		
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function refillEditFormSuccess(Nette\Application\UI\Form $form)
	{
		
	}
}
