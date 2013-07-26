<?php

namespace Framework\Application\UI;

use Nette,
	Grido;



/**
 * Secure presenter.
 */
abstract class SecuredPresenter extends BasePresenter
{
	
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		
		if (!$this->user->isLoggedIn()) {
			$this->redirect('Sign:out', array('backlink' => $this->storeRequest()));
		}
		
		if (NULL !== $this->signal) {
			$checkSignal = TRUE;
			
			if ($this->signal[1] === 'page' || $this->signal[1] === 'sort') { // Grido page & sort
				$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
				if ($component !== NULL && $component instanceof Grido\Grid) {
					$checkSignal = FALSE;
				}
			} else if ($this->signal[1] === 'submit') { // Grido filter
				$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
				if ($component !== NULL && $component instanceof Nette\Forms\Form) {
					if (is_array($component->onSuccess)) { //@TODO
						foreach ($component->onSuccess as $callback) {
							if ($callback instanceof Nette\Callback && $callback->native[0] instanceof Grido\Grid) {
								$checkSignal = FALSE;
								break;
							}
						}
					} else {
						$checkSignal = FALSE;
					}
				}
			}
		} else {
			$checkSignal = FALSE;
		}
		
		if ($checkSignal && !$this->user->isAllowed($this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->signal[1]))) {
			throw new Nette\Application\ForbiddenRequestException;
		}
		
		if (!$this->user->isAllowed($this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->getParameter('action')))) {
			throw new Nette\Application\ForbiddenRequestException;
		}
	}
}
