<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class WithUrlConfigFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['zend_boostrap']) && isset($config['zend_boostrap']['url'])
            ? $config['zend_boostrap']['url']
            : [];
        return new $requestedName($config);
    }
}
