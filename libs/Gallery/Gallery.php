<?php

namespace Gallery;


/**
 * @method \Nette\Localization\ITranslator getTranslator()
 */
class Gallery extends \Nette\Application\UI\Control
{
	/** @var int */
    protected $defaultPerPage = 48;
	
	/** @var \Nette\Localization\ITranslator */
    protected $translator;

	/** @var DataSources\IDataSource */
    protected $model;
	
	/** @var callable */
	public $imageAccessor;
	
	/** @var mixed */
    protected $data;
	
	
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

	
	public function setDefaultPerPage($perPage)
    {
        $this->defaultPerPage = (int)$perPage;
        return $this;
    }
	
	
	public function setTranslator(\Nette\Localization\ITranslator $translator)
    {
        $this->translator = $translator;
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
        return $template;
    }

	
    public function render()
    {
        $this->template->data = $this->getData();
		$this->template->render();
    }
	
	
	public function getData()
	{
		if ($this->data === NULL) {
			$this->data = $this->model->limit($this->defaultPerPage)->fetchAll();
		}
		return $this->data;
	}
	
	
	public function setImageAccessor(/* callable */$imageAccessor)
	{
		$this->imageAccessor = $imageAccessor;
	}
}
