<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CPSE\API\ProjectBundle\Document\ProjectCategory
 *
 * @ODM\Document
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")
 */
class ProjectCategory
{
    use TimestampableDocument;
    
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ODM\Field(name="name", type="string")
     */
    protected $name;

    /**
     * @var string $description
     *
     * @ODM\Field(name="description", type="string")
     */
    protected $description;
    
    /**
     * 
     * @ODM\referenceOne(targetDocument="ProjectCategory", nullable=true)
     */
    protected $parent;
    
    /**
     *
     * @ODM\referenceOne(targetDocument="Document", nullable=true)
     */
    protected $documentCategory;



    /**
     * @var CPSE\API\UserBundle\Document\APIUser
     * @Gedmo\Blameable(on="create")
     * @ODM\referenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $createdBy;
    
    /**
     * @var CPSE\API\UserBundle\Document\APIUser
     * @Gedmo\Blameable(on="update")
     * @ODM\referenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $updatedBy;
    
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    /**
     * Set createdBy
     *
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }
    
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
    

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    

    /**
     * Get createdOn
     * Convert from timestamp to datetime for display
     * @return date $createdOn
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }
    
    public function __construct() {
        //$this->created_on = new \MongoDate();
    }
    
    public function __toString() {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getDocumentCategory()
    {
        return $this->documentCategory;
    }

    public function setDocumentCategory($documentCategory)
    {
        $this->documentCategory = $documentCategory;
        return $this;
    }
 

    
 
 
}
