<?php

namespace App;

use Nette,
	Framework,
	Coraabia;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends Framework\Application\UI\BasePresenter
{
	/** @var string @persistent */
	public $backlink = '';
	
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Nette\Http\Request @inject */
	public $httpRequest;
	
		
	/**
	 * Sign-in form factory.
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addText('username', 'Login:')
			->setRequired('Zadejte přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte svoje heslo.');

		$form->addCheckbox('remember', 'Pamatovat si přihlášení')
				->setDefaultValue('checked');

		$form->addSubmit('send', 'Vstup');

		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function signInFormSucceeded($form)
	{
		$values = $form->getValues();
		$currentUser = $this->getUser();

		if ($values->remember) {
			$currentUser->setExpiration('14 days', FALSE);
		} else {
			$currentUser->setExpiration('20 minutes', TRUE);
		}

		try {
			$currentUser->login($values->username, $values->password);
			$this->updateUserLogin();
			$this->flashMessage('Byl jste úspěšně přihlášen.', 'success');
			$this->restoreRequest($this->backlink);
			$this->redirect(
					':' . ($currentUser->getIdentity()->module == Coraabia\ModuleEnum::CORAABIA ? 'Coraabia' : 'Game') . ':User:profile',
					array(
						'lang' => $currentUser->getIdentity()->lang,
						'server' => $currentUser->getIdentity()->module == Coraabia\ModuleEnum::CORAABIA ? $currentUser->getIdentity()->server : ''
					)
			);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


	public function actionIn()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->flashMessage('Už jste přihlášen.');
			$this->redirect(':Game:User:profile');
		}
	}
	
		
	public function actionOut()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout();
			$this->flashMessage('Byl jste úspěšně odhlášen.', 'warning');
		}
		$this->redirect('in');
	}
	
	
	protected function updateUserLogin()
	{
		$currentUser = $this->getUser();
		$last_login = $this->locales->timestamp;
		$last_login_ip = ip2long($this->httpRequest->getRemoteAddress());
		
		$this->game->getUserdata()->where('user_id = ?', $currentUser->getId())
				->fetch()
				->update(array(
					'last_login' => $last_login,
					'last_login_ip' => $last_login_ip
				));
		
		$currentUser->getIdentity()->last_login = $last_login;
		$currentUser->getIdentity()->last_login_ip = $last_login_ip;
	}
}
