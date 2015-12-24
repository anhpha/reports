<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CPSE\API\ProjectBundle\Document\Task
 *
 * @ODM\Document
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")
 * @ODM\HasLifecycleCallbacks
 */
class Task
{
    //CONStANTS
    const CREATED = 0;
    const ASSIGNED = 1;
    const PENDING = 2;
    const FINISHED = 3;
    
    use TimestampableDocument;
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int $project
     *
     * @ODM\ReferenceOne(targetDocument="Project")
     */
    protected $project;

    /**
     * @var string $name
     *
     * @ODM\Field(name="name", type="string")
     */
    protected $name;

    /**
     * @var string $desription
     *
     * @ODM\Field(name="desription", type="string")
     */
    protected $desription;

    /**
     * @var date $from
     *
     * @ODM\Field(name="from", type="date")
     */
    protected $from;

    /**
     * @var date $to
     *
     * @ODM\Field(name="to", type="date")
     */
    protected $to;

    /**
     * @var int $assigned_to
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $assigned_to;

    /**
     * @var int $status
     *
     * @ODM\Field(name="status", type="int")
     */
    protected $status;

    /**
     * @var date $finished_on
     *
     * @ODM\Field(name="finished_on", type="date")
     */
    protected $finished_on;

    /**
     * @var int $updated_by
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $updated_by;

    /**
     * @var collection $products
     *
     * @ODM\ReferenceMany(targetDocument="Document", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $products;

    /**
     * @var collection $related_docs
     *
     * @ODM\ReferenceMany(targetDocument="Document", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $related_docs;

    /**
     * @var collection $other_infor
     *
     * @ODM\EmbedMany(targetDocument="KeyValue")
     */
    protected $other_infor;

    /**
     * @var int $created_by
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $created_by;

    
    /**
     * 
     * @ODM\EmbedMany(targetDocument="TaskComment")
     */
    protected $comments;

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
     * Set projectId
     *
     * @param int $projectId
     * @return self
     */
    public function setProject($projectId)
    {
        $this->project = $projectId;
        return $this;
    }

    /**
     * Get projectId
     *
     * @return int $projectId
     */
    public function getProject()
    {
        return $this->project;
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
     * Set desription
     *
     * @param string $desription
     * @return self
     */
    public function setDesription($desription)
    {
        $this->desription = $desription;
        return $this;
    }

    /**
     * Get desription
     *
     * @return string $desription
     */
    public function getDesription()
    {
        return $this->desription;
    }

    /**
     * Set from
     *
     * @param timestamp $from
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Get from
     *
     * @return timestamp $from
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param timestamp $to
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Get to
     *
     * @return timestamp $to
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set assignedTo
     *
     * @param int $assignedTo
     * @return self
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assigned_to = $assignedTo;
        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return int $assignedTo
     */
    public function getAssignedTo()
    {
        return $this->assigned_to;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set finishedOn
     *
     * @param timestamp $finishedOn
     * @return self
     */
    public function setFinishedOn($finishedOn)
    {
        $this->finished_on = $finishedOn;
        return $this;
    }

    /**
     * Get finishedOn
     *
     * @return timestamp $finishedOn
     */
    public function getFinishedOn()
    {
        return $this->finished_on;
    }

    /**
     * Set updatedBy
     *
     * @param int $updatedBy
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
     * @return int $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * Set products
     *
     * @param collection $products
     * @return self
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * Get products
     *
     * @return collection $products
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set relatedDocs
     *
     * @param collection $relatedDocs
     * @return self
     */
    public function setRelatedDocs($relatedDocs)
    {
        $this->related_docs = $relatedDocs;
        return $this;
    }

    /**
     * Get relatedDocs
     *
     * @return collection $relatedDocs
     */
    public function getRelatedDocs()
    {
        return $this->related_docs;
    }

    /**
     * Set otherInfor
     *
     * @param collection $otherInfor
     * @return self
     */
    public function setOtherInfor($otherInfor)
    {
        $this->other_infor = $otherInfor;
        return $this;
    }

    /**
     * Get otherInfor
     *
     * @return collection $otherInfor
     */
    public function getOtherInfor()
    {
        return $this->other_infor;
    }

    /**
     * Set createdBy
     *
     * @param int $createdBy
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
     * @return int $createdBy
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }


    public function getComments()
    {
        return $this->comments;
    }
    
    public function addComment($newComment)
    {
        $this->comments[] = $newComment;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }
    
    public function __construct()
    {
        $this->status = self::CREATED;
        $this->comments = new ArrayCollection();
        $this->related_docs = new ArrayCollection();
    }
    
    /** @ODM\PreUpdate */
    public function handlePrePersist(){
        if ($this->assigned_to != null){
            if ($this->status == null || $this->status == self::CREATED) {
                $this->status = self::ASSIGNED;
            }
        }
        if ($this->assigned_to == null) {
            $this->status = self::CREATED;
        }
    }
    
    /** @ODM\PreFlush */
    public function handlePreFlush()
    {
        foreach ($this->related_docs as $doc) {
            $doc->setOwner($this);
        }
        if ($this->assigned_to != null){
            if ($this->status == null || $this->status == self::CREATED) {
                $this->status = self::ASSIGNED;
            }
        }
        if ($this->assigned_to == null) {
            $this->status = self::CREATED;
        }
    }
    
    
    public function addRelatedDoc($doc)
    {
        return $this->related_docs->add($doc);
    }
    public function removeRelatedDoc($doc)
    {
        $this->related_docs->removeElement($doc);
    }
    
    public function addPproduct($product) {
        return $this->products->add($product);
    }
    public function removeProduct($product)
    {
        $this->products->removeElement($product);
    }
}
