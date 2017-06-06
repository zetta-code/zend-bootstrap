<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2017 Zetta Code
 */

namespace Zetta\ZendBootstrap\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendBootstrap\Service\Thumbnail;

class ToThumbnailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $thumbnail = $container->get(Thumbnail::class);
        $toThumbnail = new ToThumbnail();
        $toThumbnail->setThumbnail($thumbnail);
        return $toThumbnail;
    }
}
