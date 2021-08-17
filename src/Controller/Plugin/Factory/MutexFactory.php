<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MutexFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['zend_boostrap']) && isset($config['zend_boostrap']['mutex'])
            ? $config['zend_boostrap']['mutex']
            : [];
        return new $requestedName($config);
    }
}
