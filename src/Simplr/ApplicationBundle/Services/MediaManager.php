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
    )
    {
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
        $mimeType = image_type_to_mime_type($this->getImageTypeFromFile($path));
        return new Response($output, 200, array('Content-Type' => $mimeType));
    }

    /**
     * @param $file
     * @return bool|int
     */
    private function getImageTypeFromFile($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            return false;
        }

        if (false === $fp = fopen($file, 'rb')) {
            return false;
        }

        if (false === $file_size = filesize($file)) {
            return false;
        }

        if ($file_size < 13) {
            return false;
        }

        if (false === $line = fread($fp, 12)) {
            return false;
        }

        fclose($fp);

        $l2 = substr($line, 0, 2);
        $l3 = substr($line, 0, 3);
        $l4 = substr($line, 0, 4);
        $l7 = substr($line, 0, 7);
        $l8 = substr($line, 0, 8);


        static $define_imagetypes = false;
        if (!$define_imagetypes) {
            !defined('IMAGETYPE_UNKNOWN') && define('IMAGETYPE_UNKNOWN', 0);
            !defined('IMAGETYPE_GIF') && define('IMAGETYPE_GIF', 1);
            !defined('IMAGETYPE_JPEG') && define('IMAGETYPE_JPEG', 2);
            !defined('IMAGETYPE_PNG') && define('IMAGETYPE_PNG', 3);
            !defined('IMAGETYPE_SWF') && define('IMAGETYPE_SWF', 4);
            !defined('IMAGETYPE_PSD') && define('IMAGETYPE_PSD', 5);
            !defined('IMAGETYPE_BMP') && define('IMAGETYPE_BMP', 6);
            !defined('IMAGETYPE_TIFF_II') && define('IMAGETYPE_TIFF_II', 7);
            !defined('IMAGETYPE_TIFF_MM') && define('IMAGETYPE_TIFF_MM', 8);
            !defined('IMAGETYPE_JPC') && define('IMAGETYPE_JPC', 9);
            !defined('IMAGETYPE_JP2') && define('IMAGETYPE_JP2', 10);
            !defined('IMAGETYPE_JPX') && define('IMAGETYPE_JPX', 11);
            !defined('IMAGETYPE_JB2') && define('IMAGETYPE_JB2', 12);
            !defined('IMAGETYPE_SWC') && define('IMAGETYPE_SWC', 13);
            !defined('IMAGETYPE_IFF') && define('IMAGETYPE_IFF', 14);
            !defined('IMAGETYPE_WBMP') && define('IMAGETYPE_WBMP', 15);
            !defined('IMAGETYPE_XBM') && define('IMAGETYPE_XBM', 16);
            !defined('IMAGETYPE_ICO') && define('IMAGETYPE_ICO', 17);
            $define_imagetypes = true;
        }


        $image_type_num = IMAGETYPE_UNKNOWN;
        if ($l2 == 'BM') {
            $image_type_num = IMAGETYPE_BMP;
        } elseif ($l3 == "\377\330\377") {
            $image_type_num = IMAGETYPE_JPEG;
        } elseif ($l3 == 'GIF') {
            $image_type_num = IMAGETYPE_GIF;
        } elseif ($l4 == "\000\000\001\000") {
            $image_type_num = IMAGETYPE_ICO;
        } elseif ($l4 == "II\052\000") {
            $image_type_num = IMAGETYPE_TIFF_II;
        } elseif ($l4 == "MM\000\052") {
            $image_type_num = IMAGETYPE_TIFF_MM;
        } elseif ($l7 == '#define') {
            $image_type_num = IMAGETYPE_XBM;
        } elseif ($l8 == "\211\120\116\107\015\012\032\012") {
            $image_type_num = IMAGETYPE_PNG;
        }

        return $image_type_num;
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
