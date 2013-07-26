<?php

namespace App;

use Nette,
	Framework;



/**
 * Sign in/out presenters.
 */
class SignPresenter extends Framework\Application\UI\BasePresenter
{
	/** @var string @persistent */
	public $backlink = ''; 

	
	
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

		$form->addCheckbox('remember', 'Pamatovat si přihlášení');

		$form->addSubmit('send', 'Vstup');

		$form->addProtection('Vypršel časový limit, odešlete formulář znovu.');
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}



	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function signInFormSucceeded($form)
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->flashMessage('Byl jste úspěšně přihlášen.', 'success');
			$this->restoreRequest($this->backlink);
			$this->redirect('User:showProfile');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}



	public function actionIn()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->flashMessage('Už jste přihlášen.');
			$this->redirect('User:showProfile');
		}
	}
	
	
	
	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste úspěšně odhlášen.', 'warning');
		$this->redirect('in');
	}
}
