<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ThumbnailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Configuration');
        $thumbnailConfig = isset($config['thumbnail']) ? $config['thumbnail'] : [];

        if (isset($thumbnailConfig['defaultPath'])) {
            $path = $thumbnailConfig['defaultPath'];
        } else {
            $path = '';
        }
        $thumbnail = new Thumbnail($path);

        if (isset($thumbnailConfig['width'])) {
            $thumbnail->setWidth($thumbnailConfig['width']);
        }

        if (isset($thumbnailConfig['height'])) {
            $thumbnail->setHeight($thumbnailConfig['height']);
        }

        return $thumbnail;
    }
}
