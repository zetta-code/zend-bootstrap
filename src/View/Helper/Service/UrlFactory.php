<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendBootstrap\View\Helper\Referer;
use Zetta\ZendBootstrap\View\Helper\Url;

class UrlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $application = $container->get('Application');
        $configuration = $container->get('Configuration');
        $config = isset($configuration['zetta-url']) ? $configuration['zetta-url'] : [];

        return new Url($application->getRequest(), $config);
    }
}
