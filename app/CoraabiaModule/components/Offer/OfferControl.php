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
				
		$grido->addColumn('price', 'Cena')
				->setCustomRender(function ($item) {
					return $item->basePrice . " " . $item->currency;
				});
				
		$grido->addColumn('count', '#')
				->setCustomRender(function ($item) {
					if ((int)$item->initialQuantity == -1) {
						return '&infin;';
					} else {
						return (int)$item->quantity . " / " . (int)$item->initialQuantity
								. " "
								. Nette\Utils\Html::el('span')
									->style('font-size: 0.8em;')
									->setText('(' . round(100 * (int)$item->quantity /(int)$item->initialQuantity, 1) . '%)');
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
		$this->getPresenter()->flashMessage('Nabídka byla smazána', 'success');
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
		
		/*$result = $this->bazaar->saveOffer(array(
			'type' => 'SHOP',
			'itemId' => 1111,
			'basePrice' => 88,
			'currency' => 'XOT',
			'quantity' => 90,
			'initialQuantity' => 100
		));*/
		
		$template->render();
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$template->render();
	}
}
