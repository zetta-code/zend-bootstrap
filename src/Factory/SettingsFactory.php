<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SettingsFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['settings_app']) ? $config['settings_app'] : [];
        $filename = isset($config['filename']) ? $config['filename'] : './data/settings.php';
        $defaultFilename = isset($config['defaultFilename']) ? $config['defaultFilename'] : './data/settings.php.dist';

        return new $requestedName($filename, false, $defaultFilename);
    }
}
