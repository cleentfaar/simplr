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
 * AclObjectIdentityAncestor
 *
 * @ORM\Table(name="acl_object_identity_ancestors")
 * @ORM\Entity
 */
class AclObjectIdentityAncestor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="object_identity_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $objectIdentityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ancestor_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ancestorId;



    /**
     * Set objectIdentityId
     *
     * @param integer $objectIdentityId
     * @return AclObjectIdentityAncestor
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
     * Set ancestorId
     *
     * @param integer $ancestorId
     * @return AclObjectIdentityAncestor
     */
    public function setAncestorId($ancestorId)
    {
        $this->ancestorId = $ancestorId;
    
        return $this;
    }

    /**
     * Get ancestorId
     *
     * @return integer 
     */
    public function getAncestorId()
    {
        return $this->ancestorId;
    }
}
