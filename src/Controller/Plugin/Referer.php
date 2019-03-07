<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Traversable;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Exception;

class Referer extends AbstractPlugin
{
    /**
     * Get referer URL based on a route
     *
     * @param string $route RouteInterface name
     * @param array|Traversable $params Parameters to use in url generation, if any
     * @param array|bool $options RouteInterface-specific options to use in url generation, if any.
     *                                                If boolean, and no fourth argument, used as $reuseMatchedParams.
     * @param bool $reuseMatchedParams Whether to reuse matched parameters
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @throws Exception\RuntimeException
     */
    public function fromRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        $controller = $this->getController();
        if (!$controller || !method_exists($controller, 'plugin')) {
            throw new Exception\DomainException('Redirect plugin requires a controller that defines the plugin() method');
        }

        $referer = $controller->getRequest()->getHeader('Referer');
        if ($referer) {
            $refererUrl = $referer->uri()->getPath(); // referer url
            $refererHost = $referer->uri()->getHost(); // referer host
            $host = $controller->getRequest()->getUri()->getHost(); // current host

            // only redirect to previous page if request comes from same host
            if ($refererUrl && ($refererHost == $host)) {
                return $refererUrl;
            }
        }
        // redirect to home if no referer or from another page
        $urlPlugin = $controller->plugin('url');
        return $urlPlugin->fromRoute($route, $params, $options, $reuseMatchedParams);
    }
}
