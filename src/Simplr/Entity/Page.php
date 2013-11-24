<?php

namespace Simplr\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="text", nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="defaults", type="text", nullable=false)
     */
    private $defaults;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="text")
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="requirements", type="text", nullable=false)
     */
    private $requirements;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text", nullable=false)
     */
    private $options;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->requirements = new ArrayCollection();
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

    /**
     * Set slug
     *
     * @param string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Page
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set defaults
     *
     * @param array $defaults
     * @return Page
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Get defaults
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set requirements
     *
     * @param array $requirements
     * @return Page
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    /**
     * Get requirements
     *
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return Page
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set active
     *
     * @param bool $active
     * @return Page
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function preSave()
    {
        $this->defaults = json_encode($this->defaults);
        $this->options = json_encode($this->options);
        $this->requirements = json_encode($this->requirements);
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->defaults = json_decode($this->defaults);
        $this->options = json_decode($this->options);
        $this->requirements = json_decode($this->requirements);
    }

}
