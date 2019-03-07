<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;

class UrlFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $request = $container->get('Application')->getRequest();
        $url = $container->get('ViewHelperManager')->get(Url::class);
        $config = $container->get('config');
        $config = isset($config['zend_boostrap']) && isset($config['zend_boostrap']['url'])
            ? $config['zend_boostrap']['url']
            : [];
        return new $requestedName($request, $url, $config);
    }
}
