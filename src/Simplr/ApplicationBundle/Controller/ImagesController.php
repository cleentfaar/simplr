<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Controller;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesController
{
    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * Constructor.
     *
     * @param DataManager $dataManager
     * @param FilterManager $filterManager
     * @param CacheManager $cacheManager
     */
    public function __construct(DataManager $dataManager, FilterManager $filterManager, CacheManager $cacheManager)
    {
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * This action applies a given filter to a given image, optionally saves the image and outputs it to the browser at the same time.
     *
     * @param Request $request
     * @param string $path
     * @param string $filter
     *
     * @return Response
     */
    public function filterAction(Request $request, $path, $filter)
    {
        /**
        $targetPath = $this->cacheManager->resolve($request, $path, $filter);
        if ($targetPath instanceof Response) {
            return $targetPath;
        }

        $image = $this->dataManager->find($filter, $path);
        $response = $this->filterManager->get($request, $filter, $image, $path);

        if ($targetPath) {
            $response = $this->cacheManager->store($response, $targetPath, $filter);
        }

        return $response;
        */
        $originalPath = $path;
        preg_match('&(-[0-9]{1,4}x[0-9]{1,4})&is', $path, $matches);
        list($width, $height) = explode('x', trim($matches[0], '-'));
        $path = preg_replace('&(-[0-9]{1,4}x[0-9]{1,4})&is', '', $path);

        $targetPath = $this->cacheManager->resolve($request, $originalPath, $filter);
        if ($targetPath instanceof Response) {
            return $targetPath;
        }

        $image = $this->dataManager->find($filter, $path);

        $filterConfig = $this->filterManager->getFilterConfiguration();
        $config = $filterConfig->get($filter);
        $config['filters']['resize']['size'] = array($width,$height);
        //$config['filters']['upscale']['min'] = array($width,$height);
        //$config['filters']['thumbnail']['size'] = array($width,$height);
        //$config['filters']['thumbnail']['mode'] = 'inset';
        //$config['filters']['relative_resize']['widen'] = $width;
        //$config['filters']['relative_resize']['heighten'] = $height;
        $filterConfig->set($filter, $config);
        $response = $this->filterManager->get($request, $filter, $image, $path);

        if ($targetPath) {
            $response = $this->cacheManager->store($response, $targetPath, $filter);
        }

        return $response;
    }
}
