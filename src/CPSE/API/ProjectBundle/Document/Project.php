<?php
namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\String;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use CPSE\API\ProjectBundle\Traits\BlameableDocument;
use Doctrine\ODM\MongoDB\Proxy\Proxy;
/**
 * CPSE\API\ProjectBundle\Document\Project
 *
 * @ODM\Document
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")
 */
class Project
{
    
    use TimestampableDocument;
    use BlameableDocument;
    
    /**
     *
     * @var MongoId $id
     *     
     *      @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var string $projectname
     *     
     *@ODM\Field(name="projectname", type="string")
     */
    protected $name;

    /**
     *
     * @var date $start
     *     
     *      @ODM\Field(name="start", type="date")
     */
    protected $start;

    /**
     *
     * @var date $end
     *     
     *      @ODM\Field(name="end", type="date")
     */
    protected $end;

    /**
     *
     * @var id $category
     *     
     * @ODM\ReferenceOne(targetDocument="ProjectCategory")
     */
    protected $category;

    /**
     *
     * @var id $pm
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $pm;

    /**
     * @var ArrayCollection
     *     
     * @ODM\ReferenceMany(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $members;

    /**
     *
     * @var string $description
     *     
     *      @ODM\Field(name="description", type="string")
     */
    protected $description;

    /**
     *
     * @var Status $status
     *     
     * @ODM\ReferenceOne(targetDocument="Status")
     */
    protected $status;
    
    /**
     * 
     * @var ArrayCollection
     * @ODM\ReferenceOne(targetDocument="Document", cascade={"persist"}, nullable=true)
     */
    protected $references;
    
    /**
     * 
     * @var ArrayCollection
     * @ODM\ReferenceOne(targetDocument="Document", cascade={"persist"}, nullable=true)
     */
    protected $products;

    /**
     *
     * @var String 
     * @ODM\Field(name="project_code", type="string")
     */
    protected $project_code;

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
     * Set start
     *
     * @param timestamp $start            
     * @return self
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Get start
     *
     * @return timestamp $start
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param timestamp $end            
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * Get end
     *
     * @return timestamp $end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set category
     *
     * @param id $category            
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return id $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set pm
     *
     * @param int $pm            
     * @return self
     */
    public function setPm($pm)
    {
        $this->pm = $pm;
        return $this;
    }

    /**
     * Get pm
     *
     * @return int $pm
     */
    public function getPm()
    {
        return $this->pm;
    }

    /**
     * Set members
     *
     * @param collection $members            
     * @return self
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }

    /**
     * Get members
     *
     * @return collection $members
     */
    public function getMembers()
    {
        foreach ($this->members as $member) {
            if ($this->doesReferencedItemExist($member) === false){
                $this->members->removeElement($member);
            }
        }
        return $this->members;
    }
    
    public function doesReferencedItemExist($item)
    {
        try {
            if ($item instanceof Proxy) {
                $item->__load();
            } elseif ($item === null) {
                return false;
            }
        } catch (\Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
            return false;
        }
        return true;
    }
    
    
    public function addMember($member)
    {
        $this->members->add($member);
        return $this;
    }
    
    public function removeMember($member)
    {
        $this->members->removeElement($member);
        return $this;
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
     * Set status
     *
     * @param Status $status            
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
     * @return Status $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getProjectCode()
    {
        return $this->project_code;
    }

    public function setProjectCode($projectCode)
    {
        $this->project_code = $projectCode;
        return $this->project_code;
    }
    
    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function setReferences($references)
    {
        $this->references = $references;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    
    public function __toString()
    {
        return $this->name;
    }
 
}
