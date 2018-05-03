<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Traversable;
use Zend\Http\Header\Referer as HeaderReferer;
use Zend\Stdlib\RequestInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

class Referer extends AbstractHelper
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Referer constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param  string $name Name of the route
     * @param  array $params Parameters for the link
     * @param  array|Traversable $options Options for the route
     * @param  bool $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                         For the link href attribute
     */
    public function __invoke($name = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        /** @var HeaderReferer $referer */
        $referer = $this->request->getHeader('Referer');
        if ($referer) {
            $uri = $referer->uri();
            $refererUrl = $uri->getPath(); // referer url
            $refererHost = $uri->getHost(); // referer host
            $host = $this->request->getUri()->getHost(); // current host

            // only redirect to previous page if request comes from same host
            if ($refererUrl && ($refererHost == $host)) {
                return $refererUrl;
            }
        }

        /** @var Url $urlHelper */
        $urlHelper = $this->view->plugin('url');
        // redirect to home if no referer or from another page
        return $urlHelper->__invoke($name, $params, $options, $reuseMatchedParams);
    }
}
