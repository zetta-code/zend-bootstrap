<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendBootstrap\Controller\Plugin\Mutex;
use Zetta\ZendBootstrap\Controller\Plugin\Url;

class UrlFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Url
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = $container->get('Configuration');
        $config = isset($configuration['zetta-url']) ? $configuration['zetta-url'] : [];
        return new Url($config);
    }
}
