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
 * AclSecurityIdentity
 *
 * @ORM\Table(name="acl_security_identities")
 * @ORM\Entity
 */
class AclSecurityIdentity
{
    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=200, nullable=false)
     */
    private $identifier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="username", type="boolean", nullable=false)
     */
    private $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set identifier
     *
     * @param string $identifier
     * @return AclSecurityIdentity
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    
        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set username
     *
     * @param boolean $username
     * @return AclSecurityIdentity
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return boolean 
     */
    public function getUsername()
    {
        return $this->username;
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