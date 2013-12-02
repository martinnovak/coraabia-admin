<?php

namespace App\GameModule;

use Nette,
	Framework;


/**
 * @method setArtistId(int)
 */
class ImageControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $artistId;
	
	
	public function renderArtistList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/artistList.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentArtistList($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editArtist');
		$removeLink = $this->lazyLink('deleteArtist');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->game->getArtists()))
				->setPrimaryKey('artist_id')
				->setDefaultSort(array('name' => 'ASC'));
		
		$grido->addColumnNumber('name', 'Jméno')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->artist_id))
							->setText($item->name);
				})
				->setFilter();
		
		$grido->addColumn('email', 'Email')
				->setSortable()
				->setCustomRender(function ($item) {
					return '<a href="mailto:' . $item->email . '">' . $item->email . '</a>';
				})
				->setFilter();
				
		$grido->addColumn('web', 'Web')
				->setSortable()
				->setCustomRender(function ($item) {
					if ($item->web) {
						$web = preg_replace('#^https?://#iA', '', $item->web);
						return '<a target="_blank" href="http://' . $web . '">' . $web . '</a>';
					} else {
						return '';
					}
				})
				->setFilter();
		
		$countries = array_merge(array('' => ''), $this->game->getCountriesAsSelect());
		$grido->addColumn('country', 'Země')
				->setSortable()
				->setCustomRender(function ($item) use ($self) {
					return $item->country ? $self->translator->translate('country.' . $item->country) : '';
				})
				->setFilter(\Grido\Components\Filters\Filter::TYPE_SELECT, $countries);
				
		$grido->addColumn('arts', 'Artů')
				->setSortable();
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->artist_id);
				})
				->setConfirm(function ($item) {
					return "Opravdu chcete smazat artistu '" . $item->name . "'?";
				});

		return $grido;
	}
	
	
	public function handleDeleteArtist()
	{
		$id = (int)$this->getParameter('id');
		try {
			$this->game->deleteArtist($id);
			$this->getPresenter()->flashMessage('Artista byl smazán.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->getPresenter()->redirect('artists');
	}
	
	
	public function renderCreateArtist()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/artistForm.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentArtistForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Artista');
		
		$form->addText('name', 'Jméno')
				->setRequired()
				->addRule(Nette\Forms\Form::FILLED, 'Vyplňte jméno artisty.');
		
		$form->addText('email', 'Email')
				->setRequired()
				->addRule(Nette\Forms\Form::FILLED, 'Vyplňte email.')
				->addRule(Nette\Forms\Form::EMAIL, 'Email má nesprávný formát.');
				
		$form->addText('web', 'Web');
		
		$countries = array_merge(array('' => ''), $this->game->getCountriesAsSelect());
		$form->addSelect('country', 'Země',  + $countries);
		
		$form->addTextArea('description', 'Poznámka')
				->setAttribute('rows', 15);
		
		$form->addGroup('Bio');
		
		$form->addTextArea('bio', 'Bio')
				->setAttribute('rows', 25);
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		if ($this->artistId != NULL) {
			$defaults = $this->game->getArtistById($this->artistId);
			$form->setDefaults($defaults);
		}
		
		$form->onSuccess[] = $this->artistFormSuccess;
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function artistFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			$row = $this->game->updateArtist($this->artistId, $values);
			$this->getPresenter()->flashMessage('Artista byl uložen.', 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Image:editArtist', array('id' => $row->artist_id));
		}
	}
	
	
	public function renderUpdateArtist()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/artistForm.latte');
		$template->render();
	}
	
	
	public function renderGallery()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/gallery.latte');
		$template->render();
	}
	
	
	public function createComponentGallery($name)
	{
		$gallery = new \Gallery\Gallery($this, $name);
		$gallery->setModel($this->game->getArts())
				->setTranslator($this->translator)
				->setImageAccessor(new \Framework\Gallery\ArtAccessor)
				->setBaseImagePath($this->getPresenter()->getContext()->parameters['resourcePath']);
		return $gallery;
	}
}
