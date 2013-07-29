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
			} else if ($this->signal[1] === 'suggest') { // Grido suggest
				$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
				if ($component !== NULL && $component instanceof Grido\Components\Filters\Filter) {
					$checkSignal = FALSE;
				}
			} else if ($this->signal[1] === 'submit') { // Grido filter
				/*$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
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
				}*/
				$checkSignal = FALSE; //@TODO
			}
		} else {
			$checkSignal = FALSE;
		}
		
		if ($checkSignal) {
			$resource = $this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->signal[1]);
			if (!$this->user->isAllowed($resource)) {
				throw new Nette\Application\ForbiddenRequestException("Zdroj '$resource' neexistuje.");
			}
		}
		
		$resource = $this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->getParameter('action'));
		if (!$this->user->isAllowed($resource)) {
			throw new Nette\Application\ForbiddenRequestException("Zdroj '$resource' neexistuje.");
		}
	}
}
