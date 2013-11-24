<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Services;

use Doctrine\ORM\EntityManager;
use Simplr\Entity\Media;

class MediaManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var null|array
     */
    private $media;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $mediaId
     * @return null|Media
     */
    public function getMedia($mediaId)
    {
        if (!isset($this->media)) {
            $this->media = $this->fetchMedia();
        }
        if (!isset($this->media[$mediaId])) {
            return null;
        }
        return $this->media[$mediaId];
    }

    /**
     * @param $path
     * @return null|Media
     */
    public function getMediaByPath($path)
    {
        $media = $this->em->getRepository('Simplr\Entity\Media')->findOneBy(array('path' => $path));
        return $media;
    }

    /**
     * @return array
     */
    private function fetchMedia()
    {
        $result = $this->em->getRepository('Simplr\Entity\Media')->findAll();
        $media = array();
        foreach ($result as $m) {
            $media[$m->getId()] = $m;
        }
        return $media;
    }
}