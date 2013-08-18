<?php

namespace App\CoraabiaModule;

use Nette,
	Framework;


/**
 * @method setNewsId(int)
 * @method int getNewsId()
 * @method setRowNumber(int)
 * @method int getRowNumber()
 */
class NewsControl extends Framework\Application\UI\BaseControl
{
	const IMAGE_MAXSIZE = 1048576;
	const IMAGE_MAXWIDTH = 614;
	const IMAGE_MAXHEIGHT = 406;
	const DATE_FORMAT = '(19|20)\d\d-(((0[13578]|1[02])-(0[1-9]|[12]\d|3[01]))|((0[469]|11)-(0[1-9]|[12]\d|30))|(02-(0[1-9]|1\d|2\d))) ([01]\d|2[0-3]):[0-5]\d:[0-5]\d';
	const VISIBLE_NEWS = 20;
	
	
	/** @var \Model\CoraabiaFactory @inject */
	public $coraabiaFactory;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $newsId;
		
	/** @var int */
	private $rowNumber = 0;
	
	
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
	public function createComponentNewslist($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('updateNews');
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
					return ($item->valid_from === NULL || $item->valid_from->getTimestamp() <= $self->locales->timestamp)
							&& ($item->valid_to === NULL || $item->valid_to->getTimestamp() >= $self->locales->timestamp)
							&& $self->rowNumber++ < $self::VISIBLE_NEWS
							? '<i class="icon-ok"></i>'
							: '';
				});
				
		$grido->addColumn('valid', 'Překlady')
				->setCustomRender(function ($item) use ($self) {
					$result = '';
					foreach ($self->locales->langs as $lang) {
						$title = 'title_' . $lang;
						$text = 'text_' . $lang;
						if ($item->$title == '' || $item->$text == '') { //intentionaly ==
							$result .= '<div class="blink"><i class="icon-warning-sign"></i>&nbsp;' . strtoupper($lang) . '</div>';
						}
					}
					return $result;
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
			$this->getPresenter()->flashMessage("Novinka '{$news->$title}' byla smazána.", 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		
		$template->news = $this->coraabiaFactory->access()->news
				->where('news_id = ?', $this->newsId)
				->fetch();
		
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentNewsForm($name)
	{
		$form = $this->formFactory->create($this, $name);

		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addText('title_' . $lang, 'Titulek')
				->setRequired()
				->addRule(Nette\Forms\Form::FILLED, 'Vyplňte titulek ' . strtoupper($lang) . ' novinky.');
			
			$form->addTextArea('text_' . $lang, 'Text')
				->setAttribute('rows', 15);
		}
		
		$form->addGroup('Platnost');
		
		$form->addText('valid_from', 'Od')
				->setRequired()
				->addRule(Nette\Forms\Form::PATTERN, 'Musí být ve formátu YYYY-MM-DD HH:MM:SS', self::DATE_FORMAT)//'(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01]) ([01]\d|2[0-3]):[0-5]\d:[0-5]\d')
				->getControlPrototype()
				->addAttributes(array('class' => 'datepicker', 'placeholder' => 'YYYY-MM-DD HH:MM:SS'));
		
		$form->addText('valid_to', 'Do')
				->addRule(Nette\Forms\Form::PATTERN, 'Musí být ve formátu YYYY-MM-DD HH:MM:SS', '|(' . self::DATE_FORMAT . ')')//'|((19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01]) ([01]\d|2[0-3]):[0-5]\d:[0-5]\d)')
				->getControlPrototype()
				->addAttributes(array('class' => 'datepicker', 'placeholder' => 'YYYY-MM-DD HH:MM:SS'));
		
		$form->addGroup('Obrázek');
		
		$form->addUpload('image_name', 'Obrázek')
				->addRule(Nette\Forms\Form::MAX_FILE_SIZE, 'Maximální velikost obrázku je ' . self::IMAGE_MAXSIZE . ' bytů.', self::IMAGE_MAXSIZE);
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		if ($this->newsId != NULL) {
			$defaults = $this->coraabiaFactory->access()->news
					->where('news_id = ?', $this->newsId)
					->fetch();
			if ($defaults->valid_from == '') { //intentionaly ==
				$defaults->update(array('valid_from' => $defaults->created));
			}
			$form->setDefaults($defaults);
			
			$this->setImage($form['image_name'], $defaults->image_name);
		} else {
			$form->setDefaults(array('valid_from' => date('Y-m-d H:i:s')));
			$this->setImage($form['image_name']);
		}
		
		$form->onSuccess[] = $this->newsFormSuccess;
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function newsFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			if ($values->image_name->isOk()) {
				if ($values->image_name->isImage()) {
					$filename = 'news/' . date('Y-m-d-His-') . \Nette\Utils\Strings::random() . '-' . $values->image_name->getSanitizedName();
					$params = $this->getPresenter()->context->getParameters();
					$imgPath = $params['resourceDir'] . '/' . $filename;
					
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
					$uploader = $params['appDir'] . "/../bin/{$this->locales->server}-image-uploader.sh $filename 2>&1";
					@exec($uploader);
				} else {
					throw new Nette\UnknownImageFileException('Nahraný soubor musí být obrázek typu GIF, PNG nebo JPEG.');
				}
			} else {
				if ($values->image_name->getName() == '') { //intentionaly ==
					unset($values->image_name); //no image was uploaded
				} else {
					throw new Nette\InvalidArgumentException('Soubor se nepodařilo nahrát.');
				}
			}
			
			if (isset($values->valid_to) && $values->valid_to == '') { //intentionaly ==
				unset($values->valid_to);
			}

			$row = $this->coraabiaFactory->access()->updateNews($this->newsId, (array)$values);
			//set image
			if (isset($values->image_name) && is_string($values->image_name)) {
				$this->setImage($form['image_name'], $values->image_name);
			}
			$this->getPresenter()->flashMessage('Novinka byla uložena.', 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($row) {
			$this->getPresenter()->redirect('News:updateNews', array('id' => $row->news_id));
		}
	}
	
	
	/**
	 * @param \Nette\Forms\Controls\UploadControl $control
	 * @param string|NULL $filename 
	 */
	protected function setImage(Nette\Forms\Controls\UploadControl $control, $filename = NULL)
	{
		if ($filename == '') { //intentionaly ==
			$control->setOption('description', 'Max. rozměry ' . self::IMAGE_MAXWIDTH . '×' . self::IMAGE_MAXHEIGHT . ', velikost ' . self::IMAGE_MAXSIZE . ' bytů.');
		} else {
			$control->setOption('description', Nette\Utils\Html::el('img')->addAttributes(array(
				'src' => $this->locales->staticUrl . '/' . $filename,
				'class' => 'img-polaroid',
				'width' => self::IMAGE_MAXWIDTH / 2,
				'height' => self::IMAGE_MAXHEIGHT / 2
			)));
		}
	}
	
	
	public function handleRemoveNewsImage()
	{
		$newsId = (int)$this->getParameter('id');
		try {
			$this->coraabiaFactory->access()->updateNews($newsId, array('image_name' => NULL));
			$this->getPresenter()->flashMessage('Obrázek byl smazán.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function renderCreate()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/newsForm.latte');
		$template->render();
	}
}
