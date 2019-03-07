<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zetta\ZendBootstrap\Service\Settings as SettingsService;

class Settings extends AbstractHelper
{
    /**
     * @var SettingsService
     */
    protected $settings;

    /**
     * SettingsHelper constructor.
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
     * @param mixed ...$keys [optional]
     * @return SettingsService|mixed
     */
    public function __invoke(...$keys)
    {
        if (count($keys) > 0) {
            return $this->settings->get($keys);
        }

        return $this->settings;
    }
}
