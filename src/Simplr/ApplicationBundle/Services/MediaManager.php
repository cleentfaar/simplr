<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Services;

use Doctrine\ORM\EntityManager;
use Gregwar\Image\Image;
use Simplr\ApplicationBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;

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
     * @var string
     */
    private $pathToMedia;

    /**
     * @var string
     */
    private $uriToMedia;

    /**
     * @param EntityManager $em
     */
    public function __construct($pathToMedia, $uriToMedia, EntityManager $em)
    {
        if (!is_dir($pathToMedia)) {
            throw new \Exception(sprintf(
                "Path provided for media is not a valid directory (%s), did you accidentally remove it?",
                $pathToMedia
            ));
        }
        $this->pathToMedia = $pathToMedia;
        $this->uriToMedia = $uriToMedia;
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

    public function getMediaUrl(Media $media, array $options = array())
    {
        $baseUrl = '/' . ltrim($this->uriToMedia, '/') . '/' . ltrim($media->getPath(), '/');
        $url = pathinfo($baseUrl, PATHINFO_DIRNAME) . '/' . pathinfo($baseUrl, PATHINFO_FILENAME);
        if (isset($options['dimensions'])) {
            $url .= '-' . $options['dimensions'];
        }
        $url .= '.' . pathinfo($baseUrl, PATHINFO_EXTENSION);
        return $url;
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
     * @param Media $media
     * @param string $targetPath
     * @param int|null $width
     * @param int|null $height
     * @param string $resizeType
     * @throws \Exception
     */
    public function resizeImageFromMedia(
        Media $media,
        $targetPath,
        $width = null,
        $height = null,
        $resizeType = 'resize'
    ) {
        $path = $media->getPath();
        $localPathAttempt = $this->pathToMedia . '/' . ltrim($path, '/');
        $localPath = realpath($localPathAttempt);
        if (!$localPath) {
            throw new \Exception(sprintf(
                "The path of the image on the filesystem does not exist, attempted %s",
                $localPath
            ));
        }
        $this->resizeImage($localPath, $targetPath, $width, $height, $resizeType);
    }


    /**
     * @param string $resizeType
     * @param $localPath
     * @param $targetPath
     * @param $width
     * @param $height
     * @param int $quality
     * @return Image
     */
    private function resizeImage($localPath, $targetPath, $width, $height, $resizeType = 'resize', $quality = 100)
    {
        $transparentExtensions = array(
            'png',
            'gif'
        );
        $extension = pathinfo($localPath, PATHINFO_EXTENSION);
        $background = null;
        if (in_array($extension, $transparentExtensions)) {
            $background = 'transparent';
        }
        $targetPath = str_replace("/", DIRECTORY_SEPARATOR, $targetPath);
        $image = Image::open($localPath);

        switch ($resizeType) {
            case 'trimmed':
                $image->cropResize($width, $height, $background);
                break;
            case 'resize':
            default:
                $image->resize($width, $height, $background);
                break;
        }
        $image->save($targetPath, 'guess', $quality);
        return $image;
    }

    /**
     * @param $path
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function outputImage($path, $quality = 100)
    {
        $image = Image::open($path);
        $output = $image->get('guess', $quality);
        $file = new File($path);
        $mimeType = $file->getMimeType();
        return new Response($output, 200, array('Content-Type' => $mimeType));
    }

    /**
     * @return array
     */
    private function fetchMedia()
    {
        $result = $this->em->getRepository('SimplrApplicationBundle:Media')->findAll();
        $media = array();
        foreach ($result as $m) {
            $media[$m->getId()] = $m;
        }
        return $media;
    }
}
