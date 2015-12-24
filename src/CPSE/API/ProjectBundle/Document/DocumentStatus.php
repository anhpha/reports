<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * CPSE\API\ProjectBundle\Document\DocumentStatus
 *
 * @ODM\Document
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")
 */
class DocumentStatus
{
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
     * @ODM\Field(name="iconHTML", type="string", nullable=true)
     */
    protected $iconHTML;

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
    
    public function __toString()
    {
        return $this->getName();
    }

    public function getIconHTML()
    {
        return $this->iconHTML;
    }

    public function setIconHTML($iconHTML)
    {
        $this->iconHTML = $iconHTML;
        return $this;
    }
 
}
