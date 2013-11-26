<?php

namespace App\CoraabiaModule;

use Framework,
	Nette;


/**
 * @method setUserId(int)
 */
class PlayerControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Coraabia @inject */
	public $coraabia;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Model\Authenticator @inject */
	public $authenticator;
	
	/** @var int */
	private $userId;

	
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
	public function createComponentPlayerlist($name)
	{
		$editLink = $this->getPresenter()->lazyLink('editPlayer');
		$revalidateLink = $this->lazyLink('revalidatePlayer');
		$borrowLink = $this->lazyLink('borrowPlayer');
		
		$players = array();
		try {
			foreach ($this->bazaar->getPlayers() as $p) {
				$players[$p->userId] = $p;
			}
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
			$players = array();
		}
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->coraabia->getPlayers()))
				->setPrimaryKey('user_id')
				->setDefaultSort(array('username' => 'ASC'));

		$grido->addColumn('user_id', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->user_id))
							->setText($item->user_id);
				})
				->setFilterNumber();

		$grido->addColumn('username', 'Jméno')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->user_id))
							->setText($item->username);
				})
				->setFilterText();

		$grido->addColumn('level', 'Level')
				->setSortable();
		
		$grido->addColumn('trin', 'Triny')
				->setCustomRender(function ($item) use ($players) {
					if (isset($players[$item->user_id])) {
						foreach ($players[$item->user_id]->amounts as $amount) {
							if ($amount->currency == 'TRI') {
								return $amount->amount;
							}
						}
					}
					return 'N/A';
				});
				
		$grido->addColumn('xot', 'Xot')
				->setCustomRender(function ($item) use ($players) {
					if (isset($players[$item->user_id])) {
						foreach ($players[$item->user_id]->amounts as $amount) {
							if ($amount->currency == 'XOT') {
								return $amount->amount;
							}
						}
					}
					return 'N/A';
				});

		$grido->addColumn('enabled', '')
				->setCustomRender(function ($item) {
					return $item->enabled ? '<i class="icon-ok"></i>' : '';
				});

		$grido->addColumn('borrowed', 'Půjčený')
				->setCustomRender(function ($item) {
					return !empty($item->password_orig) ? '<i class="icon-eye-open"></i>' : '';
				});

		$grido->addAction('borrow', 'Půjčit/Vrátit')
				->setIcon('eye-open')
				->setCustomHref(function ($item) use ($borrowLink) {
					return $borrowLink->setParameter('id', $item->user_id);
				});

		$grido->addAction('revalidate', 'Povolit/Zakázat')
				->setIcon('refresh')
				->setCustomHref(function ($item) use ($revalidateLink) {
					return $revalidateLink->setParameter('id', $item->user_id);
				});
		
		return $grido;
	}
	
	
	public function handleRevalidatePlayer()
	{
		$userId = (int)$this->getParameter('id');
		try {
			$user = $this->coraabia->getPlayerById($userId);
			$user = $this->coraabia->updatePlayer($userId, array('enabled' => !$user->enabled));
			$this->getPresenter()->flashMessage("Uživatel '" . $user->username . "' byl " . ($user->enabled ? 'povolen' : 'zakázán') . ".", 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->redirect('this');
	}
	
	
	public function handleBorrowPlayer()
	{
		$userId = (int)$this->getParameter('id');
		try {
			$user = $this->coraabia->getPlayerById($userId);
			if (empty($user->password_orig)) {
				$this->coraabia->updatePlayer($userId, array(
					'password' => $this->authenticator->getPassword($this->getPresenter()
							->getContext()
							->parameters['borrowedAccountPassword']), //@todo better
					'password_orig' => $user->password,
				));
				$this->getPresenter()->flashMessage("Uživatel '{$user->username}' je nyní půjčený.", 'info');
			} else {
				$this->coraabia->updatePlayer($userId, array(
					'password' => $user->password_orig,
					'password_orig' => NULL,
				));
				$this->getPresenter()->flashMessage("Uživatel '{$user->username}' byl vrácen.", 'info');
			}
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
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentCreatePlayerForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Registrace');
		
		$form->addText('username', 'Jméno')
				->addRule(Nette\Application\UI\Form::PATTERN, 'Jméno má špatný formát.', '[a-zA-Z][a-zA-Z0-9\._-]+');
		
		$form->addPassword('password', 'Heslo')
				->addRule(Nette\Application\UI\Form::MIN_LENGTH, 'Heslo musí být alespoň %d znaků dlouhé.', 6);
		
		$form->addText('email', 'Email')
				->addRule(Nette\Application\UI\Form::EMAIL, 'Zadejte platný email.');
		
		$languages = $this->locales->getLangs();
		$form->addSelect('language', 'Jazyk', array_combine($languages, array_map(function ($item) {
			return strtoupper($item);
		}, $languages)));
		
		$form->setCurrentGroup();
		
		$form->addSubmit('submit', 'Registrovat');
		
		$form->onSuccess[] = $this->createPlayerFormSuccess;
		
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function createPlayerFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$userId = NULL;
		try {
			$data = array(
				'agreeWithTerms' => TRUE,
				'email' => $values->email,
				'language' => $values->language,
				'password' => hash('sha256', $values->password), //@todo
				'password-lower' => $values->password,
				'tutorial-finished' => FALSE,
				'username' => $values->username
			);
			$url = $this->getPresenter()->getContext()->parameters['registerUrls'][$this->locales->server];
			
			$result = Framework\Mapi\RestApi::call($url, $data);
			
			if ($result->status == 'ERROR') {
				throw new \Exception($result->errorMessage);
			} else {
				$this->getPresenter()->flashMessage('Uživatel byl založen.', 'success');
				$userId = $this->coraabia->getPlayerByName($values['username'])->user_id;
			}
			
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($userId !== NULL) {
			$this->getPresenter()->redirect('Player:editPlayer', array('id' => $userId));
		}
	}
	
	
	public function renderEdit()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/editScripts.latte');
			
			$hook->addTemplate($tmpl);
		});
		
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentPlayerEditForm($name)
	{
		$form = $this->formFactory->create($this, $name);
	
		$form->addGroup('Uživatel');
		
		$form->addText('username', 'Jméno')
				->setRequired()
				->setDisabled()
				->setOmitted()
				->addRule(Nette\Application\UI\Form::PATTERN, 'Jméno má špatný formát.', '[a-zA-Z][a-zA-Z0-9\._-]+');
		
		$form->addPassword('password', 'Heslo')
				->addRule(Nette\Application\UI\Form::PATTERN, 'Heslo musí být alespoň 6 znaků dlouhé.', '|.{6,}');
		
		$form->addText('email', 'Email')
				->setRequired()
				->addRule(Nette\Application\UI\Form::EMAIL, 'Zadejte platný email.');
		
		$languages = $this->locales->getLangs();
		$form->addSelect('lang', 'Jazyk', array_combine($languages, array_map(function ($item) {
			return strtoupper($item);
		}, $languages)))
				->setRequired();
		
		$form->addGroup('Level');
		
		$form->addText('level', 'Level')
				->setRequired()
				->setAttribute('class', 'level')
				->setAttribute('data-bind', "value: level, valueUpdate: 'afterkeydown'")
				->addRule(Nette\Forms\Form::INTEGER);
		
		$form->addText('experience', 'Exp')
				->setRequired()
				->setAttribute('class', 'experience')
				->setAttribute('data-bind', "value: experience, valueUpdate: 'afterkeydown'")
				->addRule(Nette\Forms\Form::INTEGER);
		
		$form->addText('influence_max', 'Vliv')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		
		$form->addGroup('Peníze');
		
		$form->addText('tri', 'Triny')
				->setRequired()
				->setOmitted()
				->setDisabled();
		
		$form->addText('add_tri', 'Přidat triny')
				->setRequired()
				->addRule(\Nette\Forms\Form::INTEGER);
		
		$form->addText('xot', 'Xot')
				->setRequired()
				->setOmitted()
				->setDisabled();
		
		$form->addText('add_xot', 'Přidat xot')
				->setRequired()
				->addRule(\Nette\Forms\Form::INTEGER);
		
		
		if ($this->userId !== NULL) {
			$player = $this->coraabia->getPlayerById($this->userId)->toArray();
			unset($player['password']);
			
			try {
				$bPlayer = $this->bazaar->getPlayerById($this->userId);
				if (!$bPlayer) {
					throw new \Exception("Hráč '{$player['username']}' nebyl nalezen.");
				}
				foreach ($bPlayer->amounts as $amount) {
					$player[strtolower($amount->currency)] = $amount->amount;
				}
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
			}
			
			$player['add_tri'] = 0;
			$player['add_xot'] = 0;
			$form->setDefaults($player);
		}
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		$form->onSuccess[] = $this->playerEditFormSuccess;
		
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function playerEditFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		try {
			$player = array(
				'email' => $values->email,
				'level' => $values->level,
				'experience' => $values->experience,
				'influence_max' => $values->influence_max
			);
			//change password
			if (!empty($values->password)) {
				$player['password'] = $this->authenticator->getPassword($values->password);
			}
			
			$this->coraabia->updatePlayer($this->userId, $player);
			
			//money trin
			if ((int)$values->add_tri != 0) {
				$this->bazaar->playerAddTrin(
						$this->userId,
						(int)$values->add_tri,
						'EDITOR USER ' . $this->getPresenter()->getUser()->getId()
				);
			}
			
			//money xot
			if ((int)$values->add_xot != 0) {
				$this->bazaar->playerAddXot(
						$this->userId,
						(int)$values->add_xot,
						'EDITOR USER ' . $this->getPresenter()->getUser()->getId()
				);
			}
			
			$this->getPresenter()->flashMessage("Uživatel '{$this->userId}' byl uložen.", 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		$this->redirect('this');
	}
}
