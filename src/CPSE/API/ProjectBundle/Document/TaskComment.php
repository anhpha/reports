<?php

namespace CPSE\API\ProjectBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * CPSE\API\ProjectBundle\Document\TaskComment
 *
 * @ODM\EmbeddedDocument
 * @Vich\Uploadable
 */
class TaskComment
{
    use TimestampableDocument;
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $action
     *
     * @ODM\Field(name="action", type="string")
     */
    protected $action;

    /**
     * @var string $by
     *
     * @ODM\ReferenceOne(targetDocument="CPSE\API\UserBundle\Document\APIUser")
     */
    protected $by;

    /**
     * @var string $content
     *
     * @ODM\Field(name="content", type="string")
     */
    protected $content;
    
    /**
     * @Vich\UploadableField(mapping="projects", fileNameProperty="path")
     * @var File
     */
    private $file;
    
    /**
     * @ODM\Field(type="string", nullable=true)
     */
    private $path;
    
    /**
     * Sets file.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;
        if ($file) {
            $this->updatedAt = new \DateTime('now');
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
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get action
     *
     * @return string $action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set by
     *
     * @param APIUser $by
     * @return self
     */
    public function setBy($by)
    {
        $this->by = $by;
        return $this;
    }

    /**
     * Get by
     *
     * @return APIUser $by
     */
    public function getBy()
    {
        return $this->by;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
 
}
