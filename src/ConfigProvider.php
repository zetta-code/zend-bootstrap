<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\View\Helper\Navigation\Menu;
use Zetta\ZendBootstrap\Filter\ToThumbnail;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return $this->getConfig();
    }

    public function getConfig()
    {
        return [
            'controller_plugins' => $this->getControllerPluginConfig(),
            'filters' => $this->getFilterConfig(),
            'form_elements' => $this->getFormElementConfig(),
            'view_helpers' => $this->getViewHelpers(),
            'view_helper_config' => $this->getViewHelperConfig(),
            'navigation_helpers' => $this->getNavigationHelpersConfig(),
            'service_manager' => $this->getServiceConfig(),
            'view_manager' => $this->getViewManagerConfig(),
        ];
    }

    /**
     * Return component plugins configuration.
     *
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return [
            'aliases' => [
                'email' => Controller\Plugin\Email::class,
                'mutex' => Controller\Plugin\Mutex::class,
                'referer' => Controller\Plugin\Referer::class,
                'settings' => Controller\Plugin\Settings::class,
                'thumbnail' => Controller\Plugin\Thumbnail::class,
                'zettaUrl' => Controller\Plugin\Url::class,
            ],
            'factories' => [
                Controller\Plugin\Email::class => Controller\Plugin\Factory\EmailFactory::class,
                Controller\Plugin\Mutex::class => Controller\Plugin\Factory\MutexFactory::class,
                Controller\Plugin\Referer::class => InvokableFactory::class,
                Controller\Plugin\Settings::class => Factory\WithSettingsFactory::class,
                Controller\Plugin\Thumbnail::class => Factory\WithThumbnailFactory::class,
                Controller\Plugin\Url::class => Factory\WithUrlConfigFactory::class,
            ],
        ];
    }

    /**
     * Return component filter configuration.
     *
     * @return array
     */
    public function getFilterConfig()
    {
        return [
            'aliases' => [
                'ToThumbnail' => ToThumbnail::class,
            ],
            'factories' => [
                Filter\ToThumbnail::class => Factory\WithThumbnailFactory::class
            ],
        ];
    }

    /**
     * Return component form element configuration.
     *
     * @return array
     */
    public function getFormElementConfig()
    {
        return [
            'factories' => [
                'recaptcha' => Form\Element\ReCaptchaFactory::class
            ],
        ];
    }

    /**
     * Return component helpers configuration.
     *
     * @return array
     */
    public function getViewHelpers()
    {
        return [
            'aliases' => [
                'formmulticheckbox' => Form\View\Helper\FormMultiCheckbox::class,
                'formradio' => Form\View\Helper\FormRadio::class,
                'zettaFlashMessenger' => View\Helper\FlashMessenger::class,
                'zettaFormMultiCheckbox' => Form\View\Helper\FormMultiCheckbox::class,
                'zettaFormRadio' => Form\View\Helper\FormRadio::class,
                'zettaFormRow' => Form\View\Helper\FormRow::class,
                'zettaPaginator' => View\Helper\Paginator::class,
                'zettaReferer' => View\Helper\Referer::class,
                'settings' => View\Helper\Settings::class,
                'thumbnail' => View\Helper\Thumbnail::class,
                'zettaUrl' => View\Helper\Url::class,
            ],
            'factories' => [
                Form\View\Helper\FormMultiCheckbox::class => InvokableFactory::class,
                Form\View\Helper\FormRadio::class => InvokableFactory::class,
                Form\View\Helper\FormRow::class => InvokableFactory::class,
                View\Helper\FlashMessenger::class => InvokableFactory::class,
                View\Helper\Paginator::class => View\Helper\Factory\PaginatorFactory::class,
                View\Helper\Referer::class => View\Helper\Factory\RefererFactory::class,
                View\Helper\Settings::class => Factory\WithSettingsFactory::class,
                View\Helper\Thumbnail::class => Factory\WithThumbnailFactory::class,
                View\Helper\Url::class => View\Helper\Factory\UrlFactory::class
            ],
        ];
    }

    /**
     * Return component helpers configuration.
     *
     * @return array
     */
    public function getViewHelperConfig()
    {
        return [
            'flashmessenger' => [
                'message_open_format' => '<div%s><ul><li>',
                'message_close_string' => '</li></ul><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>',
                'message_separator_string' => '</li><li>'
            ]
        ];
    }

    /**
     * Return navigation helpers configuration.
     *
     * @return array
     */
    public function getNavigationHelpersConfig()
    {
        return [
            'aliases' => [
                Menu::class => View\Helper\Menu::class,
            ],
            'factories' => [
                View\Helper\Menu::class => InvokableFactory::class
            ]
        ];
    }

    /**
     * Return component service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Service\Settings::class => Factory\SettingsFactory::class,
                Service\Thumbnail::class => Factory\ThumbnailFactory::class,
            ],
        ];
    }

    /**
     * Return component helpers configuration.
     *
     * @return array
     */
    public function getViewManagerConfig()
    {
        return [
            'template_path_stack' => [
                __DIR__ . '/../view',
            ],
        ];
    }
}
