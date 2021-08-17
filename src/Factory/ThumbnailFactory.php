<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendBootstrap\Service\Thumbnail;

class ThumbnailFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['zend_boostrap']) && isset($config['zend_boostrap']['thumbnail'])
            ? $config['zend_boostrap']['thumbnail']
            : [];

        $thumbnail = new Thumbnail(isset($config['defaultPath']) ? $config['defaultPath'] : '');
        $thumbnail->setGirlThumbnailPath(isset($config['girlPath']) ? $config['girlPath'] : '');

        if (isset($config['width'])) {
            $thumbnail->setWidth($config['width']);
        }
        if (isset($config['height'])) {
            $thumbnail->setHeight($config['height']);
        }

        return $thumbnail;
    }
}
