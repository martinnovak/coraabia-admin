<?php

namespace Gallery\Components;

/**
 * @property-read int $page
 * @property-read array $steps
 * @property-read int $countEnd
 * @property-read int $countBegin
 * @property-write \Grido\Grid $grid
 */
class Paginator extends \Nette\Utils\Paginator
{
    /** @var int */
    protected $page;

    /** @var array */
    protected $steps = array();

    /** @var int */
    protected $countBegin;

    /** @var int */
    protected $countEnd;

    /** @var \Gallery\Gallery */
    protected $gallery;

	
    /**
     * @param \Gallery\Gallery $gallery
     * @return Paginator
     */
    public function setGallery(\Gallery\Gallery $gallery)
    {
        $this->gallery = $gallery;
        return $this;
    }

	
    /**
     * @return int
     */
    public function getPage()
    {
        if ($this->page === NULL) {
            $this->page = parent::getPage();
        }
        return $this->page;
    }

	
    /**
     * @return array
     */
    public function getSteps()
    {
        if (!$this->steps) {
            $arr = range(
                max($this->firstPage, $this->getPage() - 3),
                min($this->lastPage, $this->getPage() + 3)
            );

            $count = 4;
            $quotient = ($this->pageCount - 1) / $count;

            for ($i = 0; $i <= $count; $i++) {
                $arr[] = (int) (round($quotient * $i) + $this->firstPage);
            }

            sort($arr);
            $this->steps = array_values(array_unique($arr));
        }

        return $this->steps;
    }

	
    /**
     * @return int
     */
    public function getCountBegin()
    {
        if ($this->countBegin === NULL) {
            $this->countBegin = $this->gallery->getCount() > 0 ? $this->getOffset() + 1 : 0;
        }
        return $this->countBegin;
    }

	
    /**
     * @return int
     */
    public function getCountEnd()
    {
        if ($this->countEnd === NULL) {
            $this->countEnd = $this->gallery->getCount() > 0
                ? min($this->gallery->getCount(), $this->getPage() * $this->gallery->getPerPage())
                : 0;
        }
        return $this->countEnd;
    }
}
