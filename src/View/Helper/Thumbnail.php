<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Exception;
use Zend\View\Helper\AbstractHelper;
use Zetta\ZendBootstrap\Service\Thumbnail as ThumbnailService;

class Thumbnail extends AbstractHelper
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
