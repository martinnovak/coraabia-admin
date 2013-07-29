<?php

namespace App;

use Nette,
	Framework,
	Model,
	Grido;



/**
 * @method setNewsId(int) 
 */
class NewsControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\CoraabiaFactory @inject */
	public $coraabiaFactory;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $newsId;
	
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	
	public function createComponentNewslist($name)
	{
		$self = $this;
		$editLink = $this->presenter->lazyLink('updateNews');
		$validateLink = $this->lazyLink('validateNews');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->coraabiaFactory->access()->news)
				->setPrimaryKey('news_id')
				->setDefaultSort(array('order_by' => 'DESC'));
		
		$grido->addColumn('news_id', 'ID')
				->setSortable();
		
		$grido->addColumn('image_name', 'Obrázek')
				->setCustomRender(function ($item) use ($self) {
					if ($item->image_name != '') { //intentionaly !=
						return \Nette\Utils\Html::el('img')
								->src($self->locales->staticUrl . '/' . $item->image_name)
								->width('80')
								->height('60');
					} else {
						return '';
					}
				});
				
		
		$grido->addColumn('title_' . $this->locales->lang, 'Titulek')
				->setSortable()
				->setFilter();
		
		$grido->addColumn('text_' . $this->locales->lang, 'Text')
				->setSortable()
				->setTruncate(220)
				->setFilter();
		
		$grido->addColumnDate('order_by', 'Začátek')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->order_by->format('d.m.Y<b\r />H:i:s');
				})
				->setFilter();
		
		if ($this->locales->server == \Coraabia\ServerEnum::DEV) {
			$grido->addColumn('valid', '')
					->setCustomRender(function ($item) {
						return $item->valid ? '<i class="icon-ok"></i>' : '';
					});
		}

		$grido->addAction('validate', 'ON/OFF')
				->setIcon('check')
				->setCustomHref(function ($item) use ($validateLink) {
					return $validateLink->setParameter('id', $item->news_id);
				})
				->setDisable(function ($item) use ($self) {
					return $self->locales->server != \Coraabia\ServerEnum::DEV;
				});
				
		$grido->addAction('edit', 'Změnit')
				->setIcon('edit')
				->setCustomHref(function ($item) use ($editLink) {
					return $editLink->setParameter('id', $item->news_id);
				});
		
		return $grido;
	}
	
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$template->render();
	}
	
	
	
	public function createComponentNewsEditForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		/*$form->addTextArea('value', 'Text')
				->setAttribute('rows', 15);
		
		$form->addSubmit('submit', 'Změnit');
		
		$form->setDefaults($this->game->staticTexts->where('key = ?', $this->key)->fetch()->toArray());		
		$form->onSuccess[] = $this->textEditFormSuccess;*/
		return $form;
	}
	
	
	
	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function newsEditFormSuccess($form)
	{
		/*try {
			$this->game->updateStaticText($this->key, $form->getValues()->value);
			$this->presenter->flashMessage('Text byl uložen.', 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}*/
	}
	
	
	
	public function handleValidateNews()
	{
		$coraabia = $this->coraabiaFactory->access();
		
		$newsId = (int)$this->getParameter('id');
		$news = $coraabia->news->where('news_id = ?', $newsId)->fetch();
		
		try {
			$coraabia->validateNews($newsId, !$news->valid);
			$title = 'title_' . $this->locales->lang;
			if ($news->valid) {
				$this->presenter->flashMessage("Novinka '{$news->$title}' byla vypnuta.", 'success');
			} else {
				$this->presenter->flashMessage("Novinka '{$news->$title}' byla zapnuta.", 'success');
			}
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage, 'error');
		}
		
		$this->redirect('this');
	}
}
