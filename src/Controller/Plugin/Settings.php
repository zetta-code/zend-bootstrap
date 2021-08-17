<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Zetta\ZendBootstrap\Service\Settings as SettingsService;

class Settings extends AbstractPlugin
{
    /**
     * @var SettingsService
     */
    protected $settings;

    /**
     * Settings constructor.
     * @param SettingsService $settings
     */
    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get the Settings settings
     * @return SettingsService
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set the Settings settings
     * @param SettingsService $settings
     * @return Settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return SettingsService
     */
    public function __invoke()
    {
        return $this->settings;
    }
}
