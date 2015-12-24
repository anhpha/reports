<?php

namespace CPSE\API\UserBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * APIUser
 *
 * @MongoDB\Document
 */
class APIUser extends BaseUser
{
    const USER = 'ROLE_USER';
    const MANAGER = 'ROLE_MANAGER';
    const DEP_MANAGER = 'ROLE_DEP_MANAGER';
    const DIRECTOR = 'ROLE_DIRECTOR';
    const DEP_DIRECTOR = 'ROLE_DEP_DIECTOR';
    const ADMIN = 'ROLE_ADMIN';
    const DOC_EDITOR = 'ROLE_DOC_EDITOR';
    
    /**
     * @var integer
     *
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $apikey;
    
    /**
     * 
     * @MongoDB\String
     */
    private $fullName;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set apikey
     *
     * @param string $apikey
     * @return APIUser
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    /**
     * Get apikey
     *
     * @return string 
     */
    public function getApikey()
    {
        return $this->apikey;
    }
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }
    
    public function __toString()
    {
        if (null !== $this->getFullName() && $this->getFullName() != ''){
            return $this->getFullName();
        }
        return parent::__toString();
    }
 
}
