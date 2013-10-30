<?php

namespace App\CoraabiaModule;

use Framework,
	Nette;


/**
 * @method setOfferId(int)
 */
class OfferControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $offerId;
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	public function createComponentOfferList($name)
	{
		$self = $this;
		$baseUri = $this->template->baseUri;
		$editLink = $this->getPresenter()->lazyLink('editOffer');
		$removeLink = $this->lazyLink('deleteOffer');
		$revalidateLink = $this->lazyLink('revalidateOffer');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\MapiDataSource($this->bazaar->getShopOffers()))
				->setPrimaryKey('offerId')
				->setDefaultSort(array('offerId' => 'DESC'))
				->setPropertyAccessor(new Framework\Grido\PropertyAccessors\MapiPropertyAccessor);
		
		$grido->addColumn('offerId', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->offerId))
							->setText($item->offerId);
				});
		
		$grido->addColumn('type', 'T')
				->setCustomRender(function ($item) use ($self, $baseUri, $editLink) {
					switch ($item->itemType) {
						case 'CARD':
							$result = Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/card.png")
								. '&nbsp;'
								. Nette\Utils\Html::el('a')
									->href($editLink->setParameter('id', $item->offerId))
									->setText($self->translator->translate('card.' . $item->itemCustomId));
							break;
						default:
							$result = Nette\Utils\Html::el('a')
									->href($editLink->setParameter('id', $item->offerId))
									->setText($item->itemType);
					}
					return $result;
				});
				
		$grido->addColumn('basePrice', 'Cena')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->basePrice . " " . $item->currency;
				});
				
		$grido->addColumn('quantity', '#')
				->setSortable()
				->setCustomRender(function ($item) {
					if ((int)$item->initialQuantity == -1) {
						return '&infin;';
					} else {
						$initialQuantity = (int)$item->initialQuantity ?: 1;
						return (int)$item->quantity . " / " . $initialQuantity
								. " "
								. Nette\Utils\Html::el('span')
									->style('font-size: 0.8em;')
									->setText('(' . round(100 * (int)$item->quantity / $initialQuantity, 1) . '%)');
					}
				});
				
		$grido->addColumn('valid', '')
				->setCustomRender(function ($item) {
					return $item->valid ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->offerId);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat nabídku '{$item->offerId}'?";
				});
		
		$grido->addAction('revalidate', 'Povolit/Zakázat')
				->setIcon('refresh')
				->setCustomHref(function ($item) use ($revalidateLink) {
					return $revalidateLink->setParameter('id', $item->offerId);
				});
		
		return $grido;
	}
	
	
	public function handleDeleteOffer()
	{
		$offerId = (int)$this->getParameter('id');
		try {
			$this->bazaar->deleteOffer($offerId);
			$this->getPresenter()->flashMessage('Nabídka byla smazána.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function handleRevalidateOffer()
	{
		$offerId = (int)$this->getParameter('id');
		$offer = $this->bazaar->getShopOffers()
				->setParam('findOfferFilter', array(
					'offerId' => $offerId,
					'type' => array('SHOP')
				))
				->load();
		if (empty($offer) || count($offer) > 1) {
			$this->getPresenter()->flashMessage("Nabídka '$offerId' nebyla nalezena.", 'error');
		} else {
			$offer = $offer[0];
			$offer->valid = !$offer->valid;
			try {
				$this->bazaar->saveOffer((array)$offer);
				$this->getPresenter()->flashMessage($offer->valid ?
						"Nabídka '$offerId' byla povolena." :
						"Nabídka '$offerId' byla zakázána.", 'success');
			} catch (\Exception $e) {
				$this->getPresenter()->flashMessage($e->getMessage(), 'error');
			}
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
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentCreateOfferForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Nabídka');
		
		$items = array();
		foreach ($this->bazaar->getShopItems()->load() as $item) {
			$items[$item->itemId] = $item->itemId; //@todo
		}
		$form->addSelect('itemId', 'Položka', $items);
		
		$form->addText('basePrice', 'Cena')
				->addRule(Nette\Forms\Form::INTEGER, 'Cena musí být celé nezáporné číslo.');
		
		$form->addSelect('currency', 'Měna', array('XOT' => 'XOT', 'TRIN' => 'TRIN')); //@todo vytáhnout z modelu
		
		$form->addText('initialQuantity', 'Množství')
				->addRule(Nette\Forms\Form::INTEGER, 'Množství musí být celé nezáporné číslo.');
		
		$form->addGroup('Nastavení');
		
		$form->addSelect('valid', 'Povoleno', array(TRUE => 'Ano', FALSE => 'Ne'));
		
		$form->addText('from', 'Od'); //@todo rules
		
		$form->addText('to', 'Do'); //@todo rules
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Vytvořit');
		
		$form->onSuccess[] = $this->createOfferSuccess;
		
		return $form;
	}
	
	
	public function createOfferSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$offerId = NULL;
		
		$items = array();
		foreach ($this->bazaar->getShopItems()->load() as $item) {
			$items[$item->itemId] = $item;
		}
		
		try {
			if (!isset($items[(int)$values->itemId])) {
				throw new \Exception("Položka '{$values->itemId}' nebyla nalezena.");
			}

			$offer = array(
				'type' => 'SHOP',
				'itemId' => (int)$values->itemId,
				'basePrice' => (int)$values->basePrice,
				'currency' => $values->currency,
				'valid' => (bool)$values->valid,
				'quantity' => (int)$values->initialQuantity,
				'initialQuantity' => (int)$values->initialQuantity,
				'itemCustomId' => $items[(int)$values->itemId]->customId,
				'itemType' => $items[(int)$values->itemId]->type
			);
			if ($values->from) {
				$offer['from'] = 1000 * strtotime($values->from);
			}
			if ($values->to) {
				$offer['to'] = 1000 * strtotime($values->to);
			}
			
			$offerId = $this->bazaar->saveOffer($offer);
			$this->getPresenter()->flashMessage("Nabídka byla vytvořena.", 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		if ($offerId) {
			$this->getPresenter()->redirect('Offer:editOffer', array('id' => $offerId));
		}
	}
}
