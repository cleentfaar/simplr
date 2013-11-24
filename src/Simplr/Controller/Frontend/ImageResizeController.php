<?php
namespace Simplr\Controller\Frontend;

use Simplr\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Gregwar\Image\Image;

class ImageResizeController extends Controller
{
    /**
     * @param $path
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException
     */
    public function resizeAction()
    {
        $resizeType = 'resize';
        $uri = $this->app['request']->getRequestUri();
        $uri = substr($uri, strripos($uri, SIMPLR_URITO_MEDIA) + strlen(SIMPLR_URITO_MEDIA));
        preg_match('&(-[0-9]{1,4}x[0-9]{1,4})&is', basename($uri), $dimensions);
        $targetPath = SIMPLR_PATHTO_WEB . '/' . ltrim(SIMPLR_URITO_MEDIA, '/') . '/' . ltrim($uri, '/');
        if (file_exists($targetPath)) {
            return $this->outputImage($targetPath);
        } elseif (!is_array($dimensions) || !isset($dimensions[1])) {
            throw new UnsupportedMediaTypeHttpException("You must provide dimensions to resize the image with");
        }
        $path = preg_replace('&(-[0-9]{1,4}x[0-9]{1,4})&is', '', $uri);
        if (substr(pathinfo($path, PATHINFO_FILENAME), -8) == '-trimmed') {
            $path = preg_replace('&(-trimmed)&is', '', $path);
            $resizeType = 'trimmed';
        }
        $media = $this->app['simplr_mediamanager']->getMediaByPath($path);
        if ($media == null) {
            throw new NotFoundHttpException(sprintf("Media entity for image with path %s could not be found", $path));
        }
        $localPathAttempt = SIMPLR_PATHTO_WEB . '/' . ltrim(SIMPLR_URITO_MEDIA, '/') . '/' . ltrim($path, '/');
        $localPath = realpath($localPathAttempt);
        if (!$localPath) {
            throw new NotFoundHttpException(sprintf("The path of the image on the filesystem does not exist, attempted %s", $localPath));
        }
        list($width, $height) = explode("x", trim($dimensions[1], '-'));
        $this->resizeImage($resizeType, $localPath, $targetPath, $width, $height);
        return $this->outputImage($targetPath);
    }
    private function resizeImage($resizeType = 'resize', $localPath, $targetPath, $width, $height, $quality = 100)
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
    private function outputImage($path, $quality = 100)
    {
        $image = Image::open($path);
        $output = $image->get('guess', $quality);
        $mimeType = image_type_to_mime_type($this->getImageTypeFromFile($path));
        return new Response($output, 200, array('Content-Type' => $mimeType));
    }

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
}