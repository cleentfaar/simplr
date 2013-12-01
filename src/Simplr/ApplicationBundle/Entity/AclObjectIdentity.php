<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclObjectIdentity
 *
 * @ORM\Table(name="acl_object_identities")
 * @ORM\Entity
 */
class AclObjectIdentity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="class_id", type="integer", nullable=false)
     */
    private $classId;

    /**
     * @var string
     *
     * @ORM\Column(name="object_identifier", type="string", length=200, nullable=false)
     */
    private $objectIdentifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_object_identity_id", type="integer", nullable=true)
     */
    private $parentObjectIdentityId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entries_inheriting", type="boolean", nullable=false)
     */
    private $entriesInheriting;

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
     * @return AclObjectIdentity
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
     * Set objectIdentifier
     *
     * @param string $objectIdentifier
     * @return AclObjectIdentity
     */
    public function setObjectIdentifier($objectIdentifier)
    {
        $this->objectIdentifier = $objectIdentifier;
    
        return $this;
    }

    /**
     * Get objectIdentifier
     *
     * @return string 
     */
    public function getObjectIdentifier()
    {
        return $this->objectIdentifier;
    }

    /**
     * Set parentObjectIdentityId
     *
     * @param integer $parentObjectIdentityId
     * @return AclObjectIdentity
     */
    public function setParentObjectIdentityId($parentObjectIdentityId)
    {
        $this->parentObjectIdentityId = $parentObjectIdentityId;
    
        return $this;
    }

    /**
     * Get parentObjectIdentityId
     *
     * @return integer 
     */
    public function getParentObjectIdentityId()
    {
        return $this->parentObjectIdentityId;
    }

    /**
     * Set entriesInheriting
     *
     * @param boolean $entriesInheriting
     * @return AclObjectIdentity
     */
    public function setEntriesInheriting($entriesInheriting)
    {
        $this->entriesInheriting = $entriesInheriting;
    
        return $this;
    }

    /**
     * Get entriesInheriting
     *
     * @return boolean 
     */
    public function getEntriesInheriting()
    {
        return $this->entriesInheriting;
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
