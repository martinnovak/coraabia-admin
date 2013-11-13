<?php

namespace App\CoraabiaModule;

use Framework,
	Nette;


/**
 * @method setUserId(int)
 */
class PlayerControl extends Framework\Application\UI\BaseControl
{
	const BORROWED_PASSWORD = 'pokorchansku1238'; //@todo config
	
	/** @var \Model\CoraabiaFactory @inject */
	public $coraabiaFactory;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Framework\Application\FormFactory @inject */
	public $formFactory;
	
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
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->coraabiaFactory->access()->getPlayers())
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
				
		$grido->addColumn('enabled', '')
				->setCustomRender(function ($item) {
					return $item->enabled ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addColumn('borrowed', 'Půjčený')
				->setCustomRender(function ($item) {
					return !empty($item->password_orig) ? '<i class="icon-eye-open"></i>' : '';
				});
				
		$grido->addAction('revalidate', 'Povolit/Zakázat')
				->setIcon('refresh')
				->setCustomHref(function ($item) use ($revalidateLink) {
					return $revalidateLink->setParameter('id', $item->user_id);
				});
				
		$grido->addAction('borrow', 'Půjčit/Vrátit')
				->setIcon('eye-open')
				->setCustomHref(function ($item) use ($borrowLink) {
					return $borrowLink->setParameter('id', $item->user_id);
				});
		
		return $grido;
	}
	
	
	public function handleRevalidatePlayer()
	{
		$userId = (int)$this->getParameter('id');
		try {
			$user = $this->coraabiaFactory->access()->getPlayers()
					->where('user_id = ?', $userId)
					->fetch();
			$user->update(array('enabled' => !$user->enabled));
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
			$user = $this->coraabiaFactory->access()->getPlayers()
					->where('user_id = ?', $userId)
					->fetch();
			if (empty($user->password_orig)) {
				$user->update(array(
					'password' => $this->authenticator->getPassword(self::BORROWED_PASSWORD),
					'password_orig' => $user->password,
				));
				$this->getPresenter()->flashMessage("Uživatel '{$user->username}' je nyní půjčený.", 'info');
			} else {
				$user->update(array(
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
				$userId = $this->coraabiaFactory->access()->getPlayers()
						->select('user_id')
						->where('username = ?', $values['username'])
						->fetch()
						->user_id;
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
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
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
				->setDisabled()
				->addRule(Nette\Application\UI\Form::PATTERN, 'Jméno má špatný formát.', '[a-zA-Z][a-zA-Z0-9\._-]+');
		
		$form->addPassword('password', 'Heslo')
				->addRule(Nette\Application\UI\Form::PATTERN, 'Heslo musí být alespoň 6 znaků dlouhé.', '|.{6,}');
		
		$form->addText('email', 'Email')
				->addRule(Nette\Application\UI\Form::EMAIL, 'Zadejte platný email.');
		
		if ($this->userId !== NULL) {
			$player = $this->coraabiaFactory->access()->getPlayers()
					->where('user_id = ?', $this->userId)
					->fetch()
					->toArray();
			unset($player['password']);
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
			throw new Nette\NotImplementedException('Not yet implemented.');
			
			
			$player = array(
				
			);
			//change password
			if (!empty($values->password)) {
				$player['password'] = $this->authenticator->getPassword($values->password);
			}
			
			$this->coraabiaFactory->access()->updatePlayer($this->userId, $player);
			$this->getPresenter()->flashMessage("Uživatel '{$player['username']}' byl uložen.", 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
	}	
}
