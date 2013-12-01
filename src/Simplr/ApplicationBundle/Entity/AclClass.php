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
 * AclClass
 *
 * @ORM\Table(name="acl_classes")
 * @ORM\Entity
 */
class AclClass
{
    /**
     * @var string
     *
     * @ORM\Column(name="class_type", type="string", length=200, nullable=false)
     */
    private $classType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set classType
     *
     * @param string $classType
     * @return AclClass
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;
    
        return $this;
    }

    /**
     * Get classType
     *
     * @return string 
     */
    public function getClassType()
    {
        return $this->classType;
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
