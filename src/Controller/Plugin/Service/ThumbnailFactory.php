<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendBootstrap\Controller\Plugin\Thumbnail;
use Zetta\ZendBootstrap\Service\Thumbnail as ThumbnailService;

class ThumbnailFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Thumbnail
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Thumbnail($container->get(ThumbnailService::class));
    }
}
