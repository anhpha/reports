<?php

namespace CPSE\API\ProjectBundle\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Blameable Trait, usable with PHP >= 5.4
 *
 * @author David Buchmann <mail@davidbu.ch>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait BlameableDocument
{
    /**
     * @var CPSE\API\UserBundle\Document\APIUser
     * @Gedmo\Blameable(on="create")
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $createdBy;

    /**
     * @var CPSE\API\UserBundle\Document\APIUser
     * @Gedmo\Blameable(on="update")
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $updatedBy;

    /**
     * Sets createdBy.
     *
     * @param  CPSE\API\UserBundle\Document\APIUser $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Returns createdBy.
     *
     * @return CPSE\API\UserBundle\Document\APIUser
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Sets updatedBy.
     *
     * @param  CPSE\API\UserBundle\Document\APIUser $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Returns updatedBy.
     *
     * @return CPSE\API\UserBundle\Document\APIUser
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
