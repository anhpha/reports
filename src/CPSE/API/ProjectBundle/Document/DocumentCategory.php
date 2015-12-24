<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CPSE\API\ProjectBundle\Document\DocumentCategory
 *
 * @ODM\Document
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")
 */
class DocumentCategory
{
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ODM\ReferenceOne(targetDocument="DocumentCategory", nullable=true)
     */
    protected $parent;

    /**
     * @var string $name
     *
     * @ODM\Field(name="name", type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string $description
     *
     * @ODM\Field(name="description", type="string")
     * @Assert\Length(
     *      min = 5,
     *      max = 1024,
     *      minMessage = "Description must be at least {{ limit }} characters long",
     *      maxMessage = "Description cannot be longer than {{ limit }} characters"
     * )
     */
    protected $description;

    /**
     * @var date $created_on
     *
     * @ODM\Field(name="created_on", type="date", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $created_on;

    /**
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $created_by;

    /**
     * @var date $updated_on
     *
     * @ODM\Field(name="updated_on", type="date", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated_on;

    /**
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $updated_by;


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
     * Set parent
     *
     * @param id $parent
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return id $parent
     */
    public function getParent()
    {
        return $this->parent;
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
     * Set createdOn
     *
     * @param date $createdOn
     * @return self
     */
    public function setCreatedOn($createdOn)
    {
        $this->created_on = $createdOn;
        return $this;
    }

    /**
     * Get createdOn
     *
     * @return date $createdOn
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * Set createdBy
     *
     * @param  $createdBy
     * @return self
     */
    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return  $createdBy
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set updatedOn
     *
     * @param date $updatedOn
     * @return self
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updated_on = $updatedOn;
        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return date $updatedOn
     */
    public function getUpdatedOn()
    {
        return $this->updated_on;
    }

    /**
     * Set updatedBy
     *
     * @param  $updatedBy
     * @return self
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updated_by = $updatedBy;
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}
