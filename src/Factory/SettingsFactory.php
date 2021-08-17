<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        $filenameDefault = isset($config['filename_default']) ? $config['filename_default'] : null;

        return new $requestedName($filename, $filenameDefault, false);
    }
}
