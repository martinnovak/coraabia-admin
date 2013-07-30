<?php

namespace App;

use Nette,
	Framework,
	Model,
	Grido;



/**
 * @method setNewsId(int)
 * @method int getNewsId()
 */
class NewsControl extends Framework\Application\UI\BaseControl
{
	const IMAGE_MAXSIZE = 1048576;
	const IMAGE_MAXWIDTH = 614;
	const IMAGE_MAXHEIGHT = 406;
	
	
	/** @var \Model\CoraabiaFactory @inject */
	public $coraabiaFactory;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $newsId;
	
	
	
	public function renderList()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$this->hookManager->listen('sidebar', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/listSidebar.latte');
			$hook->addTemplate($tmpl);
		});
		$template->render();
	}
	
	
	
	public function createComponentNewslist($name)
	{
		$self = $this;
		$editLink = $this->presenter->lazyLink('updateNews');
		$removeLink = $this->lazyLink('deleteNews');
		
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
				->setFilterText()
						->setSuggestion();
		
		$grido->addColumn('text_' . $this->locales->lang, 'Text')
				->setSortable()
				->setTruncate(220)
				->setFilterText()
						->setSuggestion();
		
		$grido->addColumnDate('order_by', 'Začátek')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->order_by->format('d.m.Y<b\r />H:i:s');
				});
				
		$grido->addColumn('active', 'Aktivní')
				->setCustomRender(function ($item) use ($self) {
					return !$item->valid_to
							|| $item->valid_to->getTimestamp() >= $self->locales->timestamp
							? '<i class="icon-ok"></i>'
							: '';
				});
				
		$grido->addColumn('valid', 'Překlady')
				->setCustomRender(function ($item) use ($self) {
					foreach ($self->locales->langs as $lang) {
						if ($lang != $self->locales->lang) {
							$title = 'title_' . $lang;
							$text = 'text_' . $lang;
							if ($item->$title == '' || $item->$text == '') { //intentionaly ==
								return '<i class="icon-warning-sign"></i>&nbsp;' . strtoupper($lang);
								break;
							}
						}
					}
					return '';
				});
		
		$grido->addFilterDate('order_by', 'Začátek');

		$grido->addAction('edit', 'Změnit')
				->setIcon('edit')
				->setCustomHref(function ($item) use ($editLink) {
					return $editLink->setParameter('id', $item->news_id);
				});
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->news_id);
				})
				->setConfirm(function ($item) use ($self) {
					$title = 'title_' . $self->locales->lang;
					return "Opravdu chcete smazat novinku '{$item->$title}'?";
				});
		
		return $grido;
	}
	
	
	
	public function handleDeleteNews()
	{
		$news_id = $this->getParameter('id');
		
		try {
			$news = $this->coraabiaFactory->access()->news
					->where('news_id = ?', $news_id)
					->fetch();
			$news->delete();
			$title = 'title_' . $this->locales->lang;
			$this->presenter->flashMessage("Novinka '{$news->$title}' byla smazána.", 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	
	public function renderEdit()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		
		$template->newsImage = $this->coraabiaFactory->access()->news->select('image_name')->where('news_id = ?', $this->newsId)->fetch();
		$template->thumbWidth = self::IMAGE_MAXWIDTH / 2;
		$template->thumbHeight = self::IMAGE_MAXHEIGHT / 2;
		
		$this->hookManager->listen('sidebar', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/editSidebar.latte');
			$tmpl->newsId = $self->newsId;
			$hook->addTemplate($tmpl);
		});
		
		$template->render();
	}
	
	
	
	public function createComponentNewsForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$titleElement = $form->addText('title_' . $this->locales->lang, 'Titulek')
				->setRequired()
				->addRule(Nette\Forms\Form::FILLED, 'Vyplňte titulek novinky.');
		
		$textElement = $form->addTextArea('text_' . $this->locales->lang, 'Text')
				->setAttribute('rows', 15);
		
		$form->addText('valid_from', 'Od')
				->getControlPrototype()
				->addAttributes(array('class' => 'datepicker'));
		
		$form->addText('valid_to', 'Do')
				->getControlPrototype()
				->addAttributes(array('class' => 'datepicker'));
		
		$form->addUpload('image_name', 'Obrázek')
				->addRule(Nette\Forms\Form::MAX_FILE_SIZE, 'Maximální velikost obrázku je ' . self::IMAGE_MAXSIZE . ' bytů.', self::IMAGE_MAXSIZE);
		
		$form->addSubmit('submit', 'Uložit');
		
		if ($this->newsId !== NULL) {
			$defaults = $this->coraabiaFactory->access()->news->where('news_id = ?', $this->newsId)->fetch()->toArray();
			$form->setDefaults($defaults);
			
			//set language warnings
			$titleWarning = Nette\Utils\Html::el('');
			$textWarning = Nette\Utils\Html::el('');
			foreach ($this->locales->langs as $lang) {
				if ($lang != $this->locales->lang) {
					$title = 'title_' . $lang;
					$text = 'text_' . $lang;
					if ($defaults[$title] == '') {
						$titleWarning->add($this->getWarningElement($lang));
					}
					if ($defaults[$text] == '') {
						$textWarning->add($this->getWarningElement($lang));
					}
				}
			}
			if (count($titleWarning)) {
				$titleElement->setOption('description', $titleWarning);
			}
			if (count($textWarning)) {
				$textElement->setOption('description', $textWarning);
			}
		}
		
		$form->onSuccess[] = $this->newsFormSuccess;
		return $form;
	}
	
	
	
	/**
	 * @param string $lang 
	 */
	protected function getWarningElement($lang)
	{
		$warningElement = Nette\Utils\Html::el('div');
		$warningElement->add(Nette\Utils\Html::el('i')->addAttributes(array('class' => 'icon-warning-sign')));
		$warningElement->add('&nbsp;' . $this->translator->translate('Chybí překlad pro jazyk') . ' ' . strtoupper($lang));
		return $warningElement;
	}
	
	
	
	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function newsFormSuccess($form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			if ($values->image_name->isOk()) {
				if ($values->image_name->isImage()) {
					$filename = 'news/' . \Nette\Utils\Strings::random() . '-' . $values->image_name->getSanitizedName();
					$params = $this->presenter->context->getParameters();
					$imgPath = realpath($params['resourceDir'] . '/' . $filename);
					
					//check image size & dimensions
					$size = $values->image_name->getSize();
					if ($size > self::IMAGE_MAXSIZE) {
						throw new Nette\InvalidArgumentException("Maximální velikost obrázku je " . self::IMAGE_MAXSIZE . " bytů, nahraný obrázek má velikost $size bytů.");
					}
					$image = Nette\Image::fromFile($values->image_name->getTemporaryFile());
					if ($image->width > self::IMAGE_MAXWIDTH || $image->height > self::IMAGE_MAXHEIGHT) {
						throw new Nette\InvalidArgumentException("Maximální rozměry obrázku musí být " . self::IMAGE_MAXWIDTH . "×" . self::IMAGE_MAXHEIGHT . ", nahraný obrázek má rozměry {$image->width}×{$image->height}.");
					}
					
					//move image to final destination
					$values->image_name->move($imgPath);
					$values->image_name = $filename;
					
					//upload to static
					$uploader = realpath($params['appDir'] . "/../bin/{$this->locales->server}-image-uploader.sh $filename 2>&1");
					@exec($uploader);
				} else {
					throw new Nette\UnknownImageFileException('Nahraný soubor musí být obrázek typu GIF, PNG nebo JPEG.');
				}
			} else {
				if ($values->image_name->getName() == '') { //intentionaly ==
					unset($values->image_name);
				} else {
					throw new Nette\InvalidArgumentException('Soubor se nepodařilo nahrát.');
				}
			}
			
			if ($values->valid_from != '') { //intentionaly !=
				$values->valid_from = date('Y-m-d H:i:s', strtotime($values->valid_from));
			}
			if ($values->valid_to != '') { //intentionaly !=
				$values->valid_to = date('Y-m-d H:i:s', strtotime($values->valid_to));
			}
			$row = $this->coraabiaFactory->access()->updateNews($this->newsId, (array)$values);
			$this->presenter->flashMessage('Novinka byla uložena.', 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		if ($row) {
			$this->presenter->redirect('News:updateNews', array('id' => $row->news_id));
		}
	}
	
	
	
	public function handleRemoveNewsImage()
	{
		$newsId = (int)$this->getParameter('id');
		try {
			$this->coraabiaFactory->access()->updateNews($newsId, array('image_name' => NULL));
			$this->presenter->flashMessage('Obrázek byl smazán.', 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	
	public function renderCreate()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/create.latte');
		$template->render();
	}
}
