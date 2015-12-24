<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use CPSE\API\ProjectBundle\Helpers\Helper as Helper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CPSE\API\ProjectBundle\Document\Document
 *
 * @ODM\Document
 * @ODM\Document(repositoryClass="CPSE\API\ProjectBundle\Document\DocumentCustomRepository")
 * @Vich\Uploadable
 * @ODM\Indexes({
 *   @ODM\Index(keys={"name"="text", "description"="text"}),
 * })
 * @ODM\HasLifecycleCallbacks
 */
class Document
{
    
    const COLUMN_CREATEDBY = 'createdBy';
    const COLUMN_CREATEDON = 'createdOn';
    const COLUMN_UPDATEDBY = 'updatedBy';
    const COLUMN_UPDATEDON = 'updatedOn';
    const COLUMN_NAME = 'name';
    const COLUMN_CATEGORY = 'category_id';
    const FOLDER = 1;
    const FILE = 0;
    
    /**
     * for paging
     * @var int
     */
    public static $pageSize = 50;
    
    
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @var id $category_id
     *
     * @ODM\ReferenceOne(targetDocument="Document", nullable=true)
     */
    protected $parent;

    /**
     * @var string $name
     *
     * @ODM\Field(name="name", type="string", nullable=true)
     * @ODM\String
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters"
     * )
     */
    protected $name;

    /**
     * @var string $description
     *
     * @ODM\Field(name="description", type="string", nullable=true)
     * @ODM\String
     * @Assert\Length(
     *      min = 0,
     *      max = 10024,
     *      minMessage = "Descripton must be at least {{ limit }} characters long",
     *      maxMessage = "Descripton cannot be longer than {{ limit }} characters"
     * )
     */
    protected $description;

    /**
     * @var int $status
     *
     * @ODM\ReferenceOne(targetDocument="DocumentStatus", nullable=true)
     */
    protected $status;
    
    
    /**
     * 
     * @ODM\ReferenceOne(nullable=true)
     */
    protected $owner;
    
    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="path")
     * @var File
     */
    private $file;
    
    /**
     * 
     * @ODM\Field(name="object_type", type="integer", nullable=true)
     */
    private $type;
    
    /**
     *
     * @ODM\Field(name="filetype", type="string", nullable=true)
     */
    protected $fileType; 
    
    /**
     * 
     * @ODM\Field(name="filesize", type="float", nullable=true)
     */
    private $size;
    
    
    /**
     * @var string $name
     *
     * @ODM\Field(name="originalName", type="string")
     * @ODM\String
     */
    private $originalName;
    
    /**
     * @var string $name
     *
     * @ODM\Field(name="isNoname", type="boolean")
     * @ODM\boolean
     */
    private $isNoname;
    
    /**
     * 
     * @var array
     */
    private $children;
    
    /**
     * Sets file.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;
        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
            $this->fileType = strtoupper($file->getExtension());
            $this->size = $file->getSize();
            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $this->originalName = $file->getClientOriginalName();
                if ($this->isNoname) {
                    $this->name = $this->originalName;
                }
            }
        }
    }
    
    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
    
    
    /**
     * @ODM\Field(type="string", nullable=true)
     */
    public $path;
    
    public function getAbsolutePath()
    {
        return null === $this->path
        ? null
        : $this->getUploadRootDir().'/'.$this->path;
    }
    
    public function getWebPath()
    {
        return null === $this->path
        ? null
        : $this->getUploadDir().'/'.$this->path;
    }
    
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return '../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        
        $category = $this->getCategoryId();
        $path = $category->getName();
        while ( null !== $category->getParent() && $category->getParent()->getName() != '') {
            $category = $category->getParent();
            $path = $category->getName() .'/'.$path;
        }
        return 'documents/'.Helper::codau2khongdau($path);
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
     * Set Parent
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
     * Get categoryId
     *
     * @return id $categoryId
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
     * Set status
     *
     * @param DocumentStatus $status
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
     * @return DocumentStatus $status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    public function __toString()
    {
        return !empty($this->name)? $this->name : $this->originalName;
    }
    
    /**
     * Timestampable
     */
    
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ODM\Date
     */
    protected $updatedAt;

    /**
     * Sets createdAt.
     *
     * @param  \Datetime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * Blameable
     */
    /**
     * @var APIUser
     * @Gedmo\Blameable(on="create")
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $createdBy;
    
    /**
     * @var APIUser
     * @Gedmo\Blameable(on="update")
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser", nullable=true)
     */
    protected $updatedBy;
    
    /**
     * Sets createdBy.
     *
     * @param  APIUser $createdBy
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
     * @return APIUser
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    /**
     * Sets updatedBy.
     *
     * @param  APIUser $updatedBy
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
     * @return APIUser
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    public static function getPageSize()
    {
        return self::$pageSize;
    }

    public static function setPageSize(int $pageSize)
    {
        self::$pageSize = $pageSize;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }
 
    public function __construct($isFolder = false)
    {
        $this->type =0 ;
        $this->isNoname = false;
        if ($isFolder){
            $this->type = 1;
            $this->fileType = "Folder";
        }
    }
    
    /**
     * @ODM\PrePersist
     */
    public function handlePrePersist()
    {
        if ($this->name == null) {
            $this->isNoname = true;
            $this->name = $this->originalName;
        }
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function IsNoname()
    {
        return $this->isNoname;
    }

    public function setIsNoname($isNoname)
    {
        $this->isNoname = $isNoname;
        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
 
 
 

 
}
