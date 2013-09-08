<?php

namespace App\GameModule;

use Framework,
	Nette;


/**
 * @method setActivityId(string)
 */
class ActivityControl extends Framework\Application\UI\BaseControl
{
	const VAR_VISIBLE_DEFAULT = '1';
	const VAR_PLAYABLE_DEFAULT = '0';
	
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Framework\Application\FormFactory @inject */
	public $formFactory;
	
	/** @var string */
	private $activityId;
	
	
	public function renderActivityList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentActivityList($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editActivity');
		$removeLink = $this->lazyLink('deleteActivity');
		$baseUri = $this->template->baseUri;
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->getActivities())
				->setPrimaryKey('activity_id')
				->setDefaultSort(array('fraction' => 'ASC', 'activity_id' => 'ASC'));
		
		$grido->addColumnNumber('activity_id', 'ID')
				->setSortable();
		
		$grido->addColumn('translated_name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '" class="' . strtolower($item->fraction) . '">' . trim($self->translator->translate('activity-name.' . $item->activity_id)) . '</a>';
				});
				
		$grido->addColumn('fraction', 'F')
				->setSortable()
				->setCustomRender(function ($item) use ($baseUri) {
					return \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/" .
							($item->fraction ? strtolower($item->fraction) : 'card') .
							".png");
				});
				
		$grido->addColumn('bot_id', 'Bot')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->bot_id ? ('<i class="icon-user"></i>&nbsp;' . $item->bot->name) : '';
				});
		
		$grido->addColumn('ready', '')
				->setCustomRender(function ($item) {
					return $item->ready ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->activity_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat aktivitu '" . $self->translator->translate('activity-name.' . $item->activity_id) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteActivity()
	{
		$this->getPresenter()->flashMessage('Aktivita byla smazána.', 'success');
	}
	
	
	public function renderCreateActivity()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/createActivity.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/activityScripts.latte');
			
			$tmpl->gamerooms = array_values(array_map(function ($item) use ($self) {
				return array(
					'id' => $item->gameroom_id,
					'name' => $self->translator->translate('gameroom.' . $item->gameroom_id),
					'ready' => $item->ready,
					'ag_ready' => FALSE
				);
			}, $self->game->getGamerooms()->fetchAll()));
			
			$tmpl->activities = array_values(array_map(function ($item) use ($self) {
				return array(
					'id' => $item->activity_id,
					'name' => $self->translator->translate('activity-name.' . $item->activity_id),
					'ready' => $item->ready
				);
			}, $self->game->getActivities()->fetchAll()));
			
			$hook->addTemplate($tmpl);
		});
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\ComponentModel\IComponent
	 */
	public function createComponentActivityCreateForm($name)
	{
		$self = $this;
		$form = $this->formFactory->create($this, $name);

		$form->addGroup('Nastavení');
			
		$form->addText('activity_id', 'ID')
			->setRequired('Vyplňte ID aktivity.')
			->addRule(Nette\Forms\Form::MAX_LENGTH, 'Maximální délka ID je %value znaků.', 20)
			->addRule(Nette\Forms\Form::PATTERN, 'ID musí končit jednou z přípon _I, _II, _III, _IV', '.*_I{1,3}V?');
		
		$fractions = $this->game->getFractions();
		$fractions = array_combine(
				array_merge(array(''), $fractions),
				array_merge(array(''), array_map(function ($item) use ($self) {
					return $self->translator->translate('fraction.' . $item);
				}, $fractions))
		);
		$form->addSelect('fraction', 'Frakce', $fractions);
		
		$variantTypes = $this->game->getActivityVariants();
		$variantTypes = array_combine($variantTypes, $variantTypes);
		$form->addSelect('variant_type', 'Varianta', $variantTypes)
			->setRequired();
		
		$activityTypes = $this->game->getActivityTypes();
		$activityTypes = array_combine($activityTypes, $activityTypes);
		$form->addSelect('activity_type', 'Typ aktivity', $activityTypes)
			->setRequired();
		
		$activityStartTypes = $this->game->getActivityStartTypes();
		$activityStartTypes = array_combine($activityStartTypes, $activityStartTypes);
		$form->addSelect('start_type', 'Typ startu', $activityStartTypes)
			->setRequired();
		
		$form->addGroup('Pozice');
		
		$form->addText('tree', 'Strom')
			->setRequired('Vyplňte strom.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', 'Stromy 0-5 jsou frakční a obecný.');
		
		$form->addText('posx', 'Pozice X')
			->setRequired('Vyplňte x-ovou pozici ve stromě.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', '0 je hlavní linka stromu.');
		
		$form->addText('posy', 'Pozice Y')
			->setRequired('Vyplňte y-ovou pozici ve stromě.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', '0 je kořen stromu.');

		$form->setCurrentGroup();
		
		$form->addSubmit('submit', 'Uložit');
		
		$form->onSuccess[] = $this->activityCreateFormSuccess;
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function activityCreateFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			$this->game->getSource()->beginTransaction();
			//variables
			$visibleVar = $this->game->update('variable', NULL, array(
				'variable_id' => substr($values->activity_id . '_V', -20),
				'default_val' => self::VAR_VISIBLE_DEFAULT,
				'description' => ''
			));
			$playableVar = $this->game->update('variable', NULL, array(
				'variable_id' => substr($values->activity_id . '_P', -20),
				'default_val' => self::VAR_PLAYABLE_DEFAULT,
				'description' => ''
			));
			
			//filters
			$visibleFilter = $this->game->update('variable', NULL, array(
				'variable_id' => $visibleVar->variable_id,
				'script' => ''
			));
			$playableFilter = $this->game->update('variable', NULL, array(
				'variable_id' => $playableVar->variable_id,
				'script' => ''
			));
			
			//activity
			//$activity = $this->game->update('activity', NULL, $values); //nope
			
			/*
			//activity_filters
			$this->game->getSource()->setSelectionFactory()->table('activity_filter_visible')->insert(array(
				'activity_id' => $activity->activity_id,
				'filter_id' => $visibleFilter->filter_id
			));
			$this->game->getSource()->setSelectionFactory()->table('activity_filter_playable')->insert(array(
				'activity_id' => $activity->activity_id,
				'filter_id' => $playableFilter->filter_id
			));*/
			
			$this->game->getSource()->commit();
			return $activity;
		} catch (\Exception $e) {
			$this->game->getSource()->rollBack();
			$form->addError($e->getMessage());
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Activity:editActivity', array('id' => $row->activity_id));
		}
	}
	
	
	/**
	 * @param array $gamerooms
	 * @return string
	 */
	protected function buildGameroomList(array $gamerooms)
	{
		return implode('--', array_map(function ($item) {
			return $item->gameroom_id;
		}, $gamerooms));
	}
	
	
	/**
	 * @param string $list
	 * @return array
	 * @throws \LogicException
	 */
	protected function dissolveGameroomList($list)
	{
		$arr = explode('--', $list);
		$gamerooms = $this->game->getGamerooms()->where('gameroom_id IN ?', $arr)->fetchAll();
		if (sizeof($gamerooms) != sizeof($arr)) {
			throw new \LogicException("Cannot load some gamerooms from list '$list'.");
		} else {
			return $gamerooms;
		}
	}
}
