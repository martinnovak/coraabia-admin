<?php

namespace Gallery;

use Gallery\Components\Paginator;

/**
 * @property-read int $count
 * @property-read mixed $data
 * @property-read \Nette\Utils\Html $wrapperPrototype
 * @property-write bool $rememberState
 * @property-write int $defaultPerPage
 * @property-write string $templateFile
 * @property array $perPageList
 * @property \Nette\Localization\ITranslator $translator
 * @property \Gallery\Components\Paginator $paginator
 * @property \Gallery\DataSources\IDataSource $model
 * @property \Gallery\ImageAccessors\IImageAccessor $imageAccessor
 * @property string $baseImagePath
 */
class Gallery extends \Nette\Application\UI\Control
{
	const BUTTONS = 'buttons';
	
	/** @var int @persistent */
    public $page = 1;

	/** @var int @persistent */
    public $perPage;
	
	/** @var array */
    protected $perPageList = array(12, 24, 48, 96);
	
	/** @var int */
    protected $defaultPerPage = 24;
	
	/** @var \Nette\Localization\ITranslator */
    protected $translator;

	/** @var DataSources\IDataSource */
    protected $model;
	
	/** @var \Gallery\ImageAccessors\IImageAccessor */
	public $imageAccessor;
	
	/** @var mixed */
    protected $data;
	
	/** @var \Gallery\Components\Paginator */
    protected $paginator;
	
	/** @var int */
    protected $count;
	
	/** @var array */
    public $onFetchData;
	
	/** @var \Nette\Utils\Html */
    protected $wrapperPrototype;
	
	/** @var bool  */
    protected $rememberState = FALSE;
	
	/** @var array */
    public $onRender;
	
	public $baseImagePath = '';
	
	
    public function setModel($model, $forceWrapper = FALSE)
    {
        $this->model = $model instanceof DataSources\IDataSource && $forceWrapper === FALSE
            ? $model
            : new DataSources\Model($model);
        return $this;
    }

	
	public function setDefaultPerPage($perPage)
    {
        $this->defaultPerPage = (int)$perPage;
        if (!in_array($perPage, $this->perPageList)) {
            $this->perPageList[] = $perPage;
            sort($this->perPageList);
        }
        return $this;
    }
	
	
	public function setPerPageList(array $perPageList)
    {
        $this->perPageList = $perPageList;
        return $this;
    }
	
	
	public function setTranslator(\Nette\Localization\ITranslator $translator)
    {
        $this->translator = $translator;
        return $this;
    }
	
	
	public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }
	
	
	public function setTemplateFile($file)
    {
        $this->getTemplate()->setFile($file);
        return $this;
    }
	
	
	public function createTemplate($class = NULL)
    {
        $template = parent::createTemplate($class);
        $template->setFile(__DIR__ . '/Gallery.latte');
        $template->registerHelper('translate', callback($this->getTranslator(), 'translate'));
		$template->baseImagePath = $this->baseImagePath;
        return $template;
    }

	
    public function render()
    {
        $this->saveRememberState();
        $data = $this->getData();

        $this->template->paginator = $this->paginator;
        $this->template->data = $data;

        $this->onRender($this);
        $this->template->render();
    }
	
	
	public function getData($applyPaging = TRUE)
    {
        if ($this->model === NULL) {
            throw new \Exception('Model cannot be empty, please use method $gallery->setModel().');
        }

        if ($this->data === NULL) {
            //$this->applyFiltering();
            //$this->applySorting();

            if ($applyPaging) {
                $this->applyPaging();
            }

            $this->data = $this->model->getData();

            if ($this->data && !in_array($this->page, range(1, $this->getPaginator()->pageCount))) {
                trigger_error("Page is out of range.", E_USER_NOTICE);
                $this->page = 1;
            }

            if ($this->onFetchData) {
                $this->onFetchData($this);
            }
        }

        return $this->data;
    }
	
	
	public function setImageAccessor(ImageAccessors\IImageAccessor $imageAccessor)
	{
		$this->imageAccessor = $imageAccessor;
		return $this;
	}
	
	
	public function getImageAccessor()
	{
		if ($this->imageAccessor === NULL) {
			throw new \Nette\InvalidStateException('Image accessor cannot be empty, please use method $gallery->setImageAccessor().');
		}
		return $this->imageAccessor;
	}
	
	
	public function getCount()
    {
        if ($this->count === NULL) {
            $this->count = $this->model->getCount();
        }
        return $this->count;
    }
	
	
	public function getPerPageList()
    {
        return $this->perPageList;
    }
	
	
	public function getPerPage()
    {
        $perPage = $this->perPage === NULL
            ? $this->defaultPerPage
            : $this->perPage;

        if ($perPage !== NULL && !in_array($perPage, $this->perPageList)) {
            trigger_error("Items per page is out of range.", E_USER_NOTICE);
            $perPage = $this->defaultPerPage;
        }

        return $perPage;
    }
	
	
	protected function applyPaging()
    {
        $paginator = $this->getPaginator()
            ->setItemCount($this->getCount())
            ->setPage($this->page);

        $this['form']['count']->setValue($this->getPerPage());
        $this->model->limit($paginator->getOffset(), $paginator->getLength());
    }
	
	
	public function getPaginator()
    {
        if ($this->paginator === NULL) {
            $this->paginator = new Paginator;
            $this->paginator->setItemsPerPage($this->getPerPage())
                            ->setGallery($this);
        }
        return $this->paginator;
    }
	
	
	protected function createComponentForm($name)
    {
        $form = new \Nette\Application\UI\Form($this, $name);
        $form->setTranslator($this->getTranslator());
        $form->setMethod($form::GET);

        $buttons = $form->addContainer(self::BUTTONS);
        //$buttons->addSubmit('search', 'Search')
        //    ->onClick[] = $this->handleFilter;
        $buttons->addSubmit('reset', 'Reset')
            ->onClick[] = $this->handleReset;
        $buttons->addSubmit('perPage', 'Items per page')
            ->onClick[] = $this->handlePerPage;

        $form->addSelect('count', 'Count', array_combine($this->perPageList, $this->perPageList))
            ->controlPrototype->attrs['title'] = $this->getTranslator()->translate('Items per page');
    }
	
	
	public function handlePerPage(\Nette\Forms\Controls\SubmitButton $button)
    {
        $perPage = (int)$button->form['count']->value;
        $this->perPage = $perPage == $this->defaultPerPage
            ? NULL
            : $perPage;
        $this->page = 1;
        $this->reload();
    }
	
	
	public function reload()
    {
        if ($this->presenter->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }
	
	
	public function handleReset(\Nette\Forms\Controls\SubmitButton $button)
    {
        //$this->sort = array();
        $this->perPage = NULL;
        //$this->filter = array();
        $this->getRememberSession()->remove();
        //$button->form->setValues(array(Filter::ID => $this->defaultFilter), TRUE);
        $this->page = 1;
        $this->reload();
    }
	
	
	public function getRememberSession()
    {
        return $this->presenter->getSession($this->presenter->name . '\\' . ucfirst($this->name));
    }
	
	
	public function getWrapperPrototype()
    {
        if ($this->wrapperPrototype === NULL) {
            $this->wrapperPrototype = \Nette\Utils\Html::el('div')
                ->id($this->name)
                ->class('gallery');
        }
        return $this->wrapperPrototype;
    }
	
	
	protected function saveRememberState()
    {
        if ($this->rememberState) {
            $session = $this->getRememberSession();
            $session->params = $this->params;
        }
    }
	
	
	public function setRememberState($state = TRUE)
    {
        $this->rememberState = (bool)$state;
        return $this;
    }
	
	
	public function getTranslator()
    {
        /*if ($this->translator === NULL) {
            $this->setTranslator(new Translations\FileTranslator);
        }*/
        return $this->translator;
    }
	
	
	public function handlePage($page)
    {
        $this->reload();
    }
	
	
	public function getBaseImagePath()
	{
		return $this->baseImagePath;
	}
	
	
	public function setBaseImagePath($path)
	{
		$this->baseImagePath = $path;
		return $this;
	}
}
