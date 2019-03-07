<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Service;

use Exception;

class Thumbnail
{
    /**
     * @var string
     */
    protected $defaultThumbnailPath;

    /**
     * @var string
     */
    protected $girlThumbnailPath;

    /**
     * @var string
     */
    protected $target = null;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * ImageThumbPlugin constructor.
     * @param string $defaultThumbnailPath
     * @param int $width
     * @param int $height
     */
    public function __construct($defaultThumbnailPath, $width = 128, $height = 128)
    {
        $this->defaultThumbnailPath = $defaultThumbnailPath;
        $this->width = $width;
        $this->height = $height;
    }


    /**
     * @param string $path
     * @param null $width
     * @param null $height
     * @return resource|string
     */
    private function makeThumbnail($path, $width = null, $height = null)
    {
        //File Resize Crop to Blob All in One
        switch (exif_imagetype($path)) {
            case IMAGETYPE_GIF :
                $image = imagecreatefromgif($path);
                break;
            case IMAGETYPE_JPEG :
                $image = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG :
                $image = imagecreatefrompng($path);
                break;
            default :
                if ('image/svg+xml' === mime_content_type($path)) {
                    return 'image/svg+xml';
                } else {
                    throw new Exception('Invalid image type');
                }
        }
        if ($width === null) {
            $width = $this->width;
        }
        if ($height === null) {
            $height = $this->height;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        $originalAspect = $originalWidth / $originalHeight;
        $thumbAspect = 1.0;

        if ($originalAspect >= $thumbAspect) {
            $newHeight = $height;
            $newWidth = $originalWidth / ($originalHeight / $height);
        } else {
            $newWidth = $width;
            $newHeight = $originalHeight / ($originalWidth / $width);
        }

        $thumbnail = imagecreatetruecolor($width, $height);
        $alphaChannel = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
        imagecolortransparent($thumbnail, $alphaChannel);
        imagefill($thumbnail, 0, 0, $alphaChannel);

        imagecopyresampled(
            $thumbnail,
            $image,
            0 - ($newWidth - $width) / 2, // Center the image horizontally
            0 - ($newHeight - $height) / 2, // Center the image vertically
            0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        imagesavealpha($thumbnail, true);
        return $thumbnail;
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
        $thumbnail = $this->makeThumbnail($path, $width, $height);

        if ($target === null) {
            ob_start();
            imagepng($thumbnail, null, 9, PNG_ALL_FILTERS);
            $return = 'data:image/png;base64,' . base64_encode(ob_get_contents());
            ob_end_clean();
        } else {
            if (preg_match('/^data:([a-z]+\/[a-z]+(;[a-z\-]+\=[a-z\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*$/i', trim($target))) {
                $info = pathinfo($this->getTarget());
                $newTarget = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . str_replace('.', '_', uniqid('_', true));
                if (isset($info['extension'])) {
                    $newTarget .= '.' . $info['extension'];
                }
                $target = $newTarget;
            }
            if ($thumbnail !== 'image/svg+xml') {
                imagepng($thumbnail, $target, 9, PNG_ALL_FILTERS);
            }
            $return = $target;
        }

        imagedestroy($thumbnail);
        return $return;
    }

    /**
     * Get the Thumbnail defaultThumbnailPath
     * @return string
     */
    public function getDefaultThumbnailPath()
    {
        return $this->defaultThumbnailPath;
    }

    /**
     * Set the Thumbnail defaultThumbnailPath
     * @param string $defaultThumbnailPath
     * @return Thumbnail
     */
    public function setDefaultThumbnailPath($defaultThumbnailPath)
    {
        $this->defaultThumbnailPath = $defaultThumbnailPath;
        return $this;
    }

    /**
     * Get the Thumbnail girlThumbnailPath
     * @return string
     */
    public function getGirlThumbnailPath()
    {
        return $this->girlThumbnailPath;
    }

    /**
     * Set the Thumbnail girlThumbnailPath
     * @param string $girlThumbnailPath
     * @return Thumbnail
     */
    public function setGirlThumbnailPath($girlThumbnailPath)
    {
        $this->girlThumbnailPath = $girlThumbnailPath;
        return $this;
    }

    /**
     * Get the Thumbnail target
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the Thumbnail target
     * @param string $target
     * @return Thumbnail
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get the Thumbnail width
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the Thumbnail width
     * @param int $width
     * @return Thumbnail
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get the Thumbnail height
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the Thumbnail height
     * @param int $height
     * @return Thumbnail
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function getDefaultThumbnail()
    {
        return $this->process($this->defaultThumbnailPath);
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function getGirlThumbnail()
    {
        return $this->process($this->girlThumbnailPath);
    }
}
