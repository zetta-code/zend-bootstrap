<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Exception;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Zetta\ZendBootstrap\Service\Thumbnail as ThumbnailService;

class Thumbnail extends AbstractPlugin
{
    /**
     * @var ThumbnailService
     */
    protected $thumbnail;

    /**
     * Thumbnail constructor.
     * @param ThumbnailService $thumbnail
     */
    public function __construct(ThumbnailService $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return ThumbnailService
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param ThumbnailService $thumbnail
     * @return Thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @param string $path
     * @param null $target
     * @param null $width
     * @param null $height
     * @return null|string
     */
    public function process($path, $target = null, $width = null, $height = null)
    {
        return $this->thumbnail->process($path, $target, $width, $height);
    }

    /**
     * @return string
     */
    public function getDefaultThumbnailPath()
    {
        return $this->thumbnail->getDefaultThumbnailPath();
    }

    /**
     * @return string
     */
    public function getGirlThumbnailPath()
    {
        return $this->thumbnail->getGirlThumbnailPath();
    }

    /**
     * @return null|string
     */
    public function getDefaultThumbnail()
    {
        try {
            return $this->thumbnail->getDefaultThumbnail();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    public function getGirlThumbnail()
    {
        try {
            return $this->thumbnail->getGirlThumbnail();
        } catch (Exception $exception) {
            return null;
        }
    }
}
