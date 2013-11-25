<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Controller\Frontend;

use Simplr\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class ImageResizeController
 * @package Simplr\Controller\Frontend
 */
class ImageResizeController extends Controller
{
    /**
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException
     */
    public function resizeAction()
    {
        $resizeType = 'resize';
        $uri = $this->app['request']->getRequestUri();
        $uri = substr($uri, strripos($uri, SIMPLR_URITO_MEDIA) + strlen(SIMPLR_URITO_MEDIA));
        $targetPath = SIMPLR_PATHTO_WEB . '/' . ltrim(SIMPLR_URITO_MEDIA, '/') . '/' . ltrim($uri, '/');
        if (file_exists($targetPath)) {
            return $this->app['simplr_mediamanager']->outputImage($targetPath);
        }
        preg_match('&(-[0-9]{1,4}x[0-9]{1,4})&is', basename($uri), $dimensions);
        if (!is_array($dimensions) || !isset($dimensions[1])) {
            throw new UnsupportedMediaTypeHttpException("You must provide dimensions to resize the image with");
        }
        $path = preg_replace('&(-[0-9]{1,4}x[0-9]{1,4})&is', '', $uri);
        if (substr(pathinfo($path, PATHINFO_FILENAME), -8) == '-trimmed') {
            $path = preg_replace('&(-trimmed)&is', '', $path);
            $resizeType = 'trimmed';
        }
        list($width, $height) = explode("x", trim($dimensions[1], '-'));
        $media = $this->app['simplr_mediamanager']->getMediaByPath($path);
        if ($media === null) {
            throw new NotFoundHttpException(sprintf("Could not find media object with path %s", $path));
        }
        $this->app['simplr_mediamanager']->resizeImageFromMedia($media, $targetPath, $width, $height, $resizeType);
        return $this->app['simplr_mediamanager']->outputImage($targetPath);
    }
}