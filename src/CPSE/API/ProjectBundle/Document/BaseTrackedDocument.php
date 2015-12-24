<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * CPSE\API\ProjectBundle\Document\BaseTrackedDocument
 *
 * @ODM\MappedSuperclass
 */
class BaseTrackedDocument
{

    /**
     * @var string $created_by
     *
     * @ODM\Field(name="created_by", type="string")
     */
    protected $created_by;

    /**
     * @var date $created_on
     *
     * @ODM\Field(name="created_on", type="date")
     */
    protected $created_on;

    /**
     * @var string $updated_by
     *
     * @ODM\Field(name="updated_by", type="string")
     */
    protected $updated_by;

    /**
     * @var date $updated_on
     *
     * @ODM\Field(name="updated_on", type="date")
     */
    protected $updated_on;


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
     * Set createdBy
     *
     * @param string $createdBy
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
     * @return string $createdBy
     */
    public function getCreatedBy()
    {
        return $this->created_by;
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
     * Set updatedBy
     *
     * @param string $updatedBy
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
     * @return string $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
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
}
