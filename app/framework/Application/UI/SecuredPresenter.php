<?php

namespace Framework\Application\UI;

use Nette,
	Grido,
	Gallery;


/**
 * Secure presenter.
 */
abstract class SecuredPresenter extends BasePresenter
{
	/**
	 * @param mixed $element
	 * @throws Nette\Application\ForbiddenRequestException 
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Sign:out', array('backlink' => $this->storeRequest()));
		}
		
		//if (NULL !== $this->signal) {
		//	$checkSignal = TRUE;
		//	
		//	if ($this->signal[1] === 'page' || $this->signal[1] === 'sort') { // Grido/Gallery page & sort
		//		$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
		//		if ($component !== NULL && ($component instanceof Grido\Grid || $component instanceof Gallery\Gallery)) {
		//			$checkSignal = FALSE;
		//		}
		//	} else if ($this->signal[1] === 'suggest') { // Grido suggest
		//		$component = $this->signal[0] === '' ? $this : $this->getComponent($this->signal[0], FALSE);
		//		if ($component !== NULL && $component instanceof Grido\Components\Filters\Filter) {
		//			$checkSignal = FALSE;
		//		}
		//	} else if ($this->signal[1] === 'submit') { // form submit
		//		/* Form submit permissions are checked by the page the form is on, so just skip checking this signal. */
		//		$checkSignal = FALSE;
		//	}
		//} else {
		//	$checkSignal = FALSE;
		//}
		
		//if ($checkSignal) {
		if ($this->signal !== NULL) {
			$resource = $this->getUser()->getAuthorizator()->buildResourceName($this->signal[1]);
			if (!$this->getUser()->isAllowed($resource)) {
				throw new Nette\Application\ForbiddenRequestException("Nemáte práva na zdroj '$resource'.");
			}
		}
		
		$resource = $this->getUser()->getAuthorizator()->buildResourceName($this->getParameter('action'));
		if (!$this->getUser()->isAllowed($resource)) {
			throw new Nette\Application\ForbiddenRequestException("Nemáte práva na zdroj '$resource'.");
		}
	}
}
