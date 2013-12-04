<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclEntry
 *
 * @ORM\Table(name="acl_entries")
 * @ORM\Entity
 */
class AclEntry
{
    /**
     * @var integer
     *
     * @ORM\Column(name="class_id", type="integer", nullable=false)
     */
    private $classId;

    /**
     * @var integer
     *
     * @ORM\Column(name="object_identity_id", type="integer", nullable=true)
     */
    private $objectIdentityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="security_identity_id", type="integer", nullable=false)
     */
    private $securityIdentityId;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=50, nullable=true)
     */
    private $fieldName;

    /**
     * @var integer
     *
     * @ORM\Column(name="ace_order", type="integer", nullable=false)
     */
    private $aceOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="mask", type="integer", nullable=false)
     */
    private $mask;

    /**
     * @var boolean
     *
     * @ORM\Column(name="granting", type="boolean", nullable=false)
     */
    private $granting;

    /**
     * @var string
     *
     * @ORM\Column(name="granting_strategy", type="string", length=30, nullable=false)
     */
    private $grantingStrategy;

    /**
     * @var boolean
     *
     * @ORM\Column(name="audit_success", type="boolean", nullable=false)
     */
    private $auditSuccess;

    /**
     * @var boolean
     *
     * @ORM\Column(name="audit_failure", type="boolean", nullable=false)
     */
    private $auditFailure;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set classId
     *
     * @param integer $classId
     * @return AclEntry
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
    
        return $this;
    }

    /**
     * Get classId
     *
     * @return integer 
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * Set objectIdentityId
     *
     * @param integer $objectIdentityId
     * @return AclEntry
     */
    public function setObjectIdentityId($objectIdentityId)
    {
        $this->objectIdentityId = $objectIdentityId;
    
        return $this;
    }

    /**
     * Get objectIdentityId
     *
     * @return integer 
     */
    public function getObjectIdentityId()
    {
        return $this->objectIdentityId;
    }

    /**
     * Set securityIdentityId
     *
     * @param integer $securityIdentityId
     * @return AclEntry
     */
    public function setSecurityIdentityId($securityIdentityId)
    {
        $this->securityIdentityId = $securityIdentityId;
    
        return $this;
    }

    /**
     * Get securityIdentityId
     *
     * @return integer 
     */
    public function getSecurityIdentityId()
    {
        return $this->securityIdentityId;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     * @return AclEntry
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    
        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string 
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set aceOrder
     *
     * @param integer $aceOrder
     * @return AclEntry
     */
    public function setAceOrder($aceOrder)
    {
        $this->aceOrder = $aceOrder;
    
        return $this;
    }

    /**
     * Get aceOrder
     *
     * @return integer 
     */
    public function getAceOrder()
    {
        return $this->aceOrder;
    }

    /**
     * Set mask
     *
     * @param integer $mask
     * @return AclEntry
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    
        return $this;
    }

    /**
     * Get mask
     *
     * @return integer 
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Set granting
     *
     * @param boolean $granting
     * @return AclEntry
     */
    public function setGranting($granting)
    {
        $this->granting = $granting;
    
        return $this;
    }

    /**
     * Get granting
     *
     * @return boolean 
     */
    public function getGranting()
    {
        return $this->granting;
    }

    /**
     * Set grantingStrategy
     *
     * @param string $grantingStrategy
     * @return AclEntry
     */
    public function setGrantingStrategy($grantingStrategy)
    {
        $this->grantingStrategy = $grantingStrategy;
    
        return $this;
    }

    /**
     * Get grantingStrategy
     *
     * @return string 
     */
    public function getGrantingStrategy()
    {
        return $this->grantingStrategy;
    }

    /**
     * Set auditSuccess
     *
     * @param boolean $auditSuccess
     * @return AclEntry
     */
    public function setAuditSuccess($auditSuccess)
    {
        $this->auditSuccess = $auditSuccess;
    
        return $this;
    }

    /**
     * Get auditSuccess
     *
     * @return boolean 
     */
    public function getAuditSuccess()
    {
        return $this->auditSuccess;
    }

    /**
     * Set auditFailure
     *
     * @param boolean $auditFailure
     * @return AclEntry
     */
    public function setAuditFailure($auditFailure)
    {
        $this->auditFailure = $auditFailure;
    
        return $this;
    }

    /**
     * Get auditFailure
     *
     * @return boolean 
     */
    public function getAuditFailure()
    {
        return $this->auditFailure;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
