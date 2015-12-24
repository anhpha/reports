<?php

namespace CPSE\API\ProjectBundle\Classes;

use CPSE\API\ProjectBundle\Document\Document;

class DocumentSearchQuery 
{
    /**
     * search term
     * @var string
     */
    private $q;
    
    /**
     * sort column 
     * @var string
     */
    private $sortedBy;
    
    /**
     */
    private $order;
    
    private $currentPage;
    
    private $year;
    
    private $category;
    
    private $project;

    public function getQ()
    {
        return $this->q;
    }

    public function setQ($q)
    {
        $this->q = $q;
        return $this;
    }

    public function getSortedBy()
    {
        return $this->sortedBy;
    }

    public function setSortedBy($sortedBy)
    {
        $this->sortedBy = $sortedBy;
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }
    
    public function setDeaultValue() {
        if (null === $this->getSortedBy()){
            $this->setSortedBy(Document::COLUMN_UPDATEDON);
        }
        if (null === $this->getOrder()) {
            $this->setOrder('ASC');
        }
        if (null === $this->getCurrentPage()){
            $this->setCurrentPage(1);
        }
    }
 
}
