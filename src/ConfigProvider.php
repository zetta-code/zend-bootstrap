<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\Controller\Plugin\Url;
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
            'view_helpers' => $this->getViewHelpers(),
            'view_helper_config' => $this->getViewHelperConfig(),
            'navigation_helpers' => [
                'aliases' => [
                    Menu::class => View\Helper\Menu::class,
                ],
                'factories' => [
                    View\Helper\Menu::class => InvokableFactory::class
                ]
            ],
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
                'thumbnail' => Controller\Plugin\Thumbnail::class,
                'referer' => Controller\Plugin\Referer::class
            ],
            'factories' => [
                Controller\Plugin\Email::class => Controller\Plugin\Service\EmailFactory::class,
                Controller\Plugin\Mutex::class => Controller\Plugin\Service\MutexFactory::class,
                Controller\Plugin\Thumbnail::class => Controller\Plugin\Service\ThumbnailFactory::class,
                Controller\Plugin\Referer::class => InvokableFactory::class,
                Controller\Plugin\Url::class => Controller\Plugin\Service\UrlFactory::class
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
                Filter\ToThumbnail::class => Filter\ToThumbnailFactory::class
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
                'zettaFlashMessenger' => View\Helper\FlashMessenger::class,
                'zettaFormRow' => Form\View\Helper\FormRow::class,
                'zettaPaginator' => View\Helper\Paginator::class,
                'zettaReferer' => View\Helper\Referer::class,
            ],
            'factories' => [
                Form\View\Helper\FormRow::class => InvokableFactory::class,
                View\Helper\FlashMessenger::class => InvokableFactory::class,
                View\Helper\Paginator::class => View\Helper\Service\PaginatorFactory::class,
                View\Helper\Referer::class => View\Helper\Service\RefererFactory::class,
                View\Helper\Url::class => View\Helper\Service\UrlFactory::class
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
                'message_open_format' => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
                'message_close_string' => '</li></ul></div>',
                'message_separator_string' => '</li><li>'
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
                Service\Thumbnail::class => Service\ThumbnailFactory::class
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
