<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Traversable;
use Zend\Stdlib\RequestInterface;
use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url as UrlHelper;

class Url extends AbstractHelper
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var bool
     */
    protected $reuseQuery = false;

    /**
     * Referer constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request, $config)
    {
        $this->request = $request;

        if (isset($config['reuse-query']) && !empty($config['reuse-query'])) {
            $this->setReuseQuery($config['reuse-query']);
        }
    }

    /**
     * @param  string $name Name of the route
     * @param  array $params Parameters for the link
     * @param  array|Traversable $options Options for the route
     * @param  bool $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                         For the link href attribute
     */
    public function __invoke($name = null, $params = [], $options = [], $reuseMatchedParams = false, $reuseQueriedParams = null)
    {
        if (null === $this->request) {
            throw new Exception\RuntimeException('No RequestInterface instance provided');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $reuseQueriedParams = null;
            $options = [];
        }
        if (4 == func_num_args() && is_bool($options)) {
            $reuseQueriedParams = $reuseMatchedParams;
            $reuseMatchedParams = $options;
            $options = [];
        }

        if ($reuseQueriedParams === null) {
            $reuseQueriedParams = $this->isReuseQuery();
        }
        $query = $this->request->getQuery()->toArray();
        if ($reuseQueriedParams && $query) {
            $options = array_merge(['query' => $query], $options);
        }

        return $this->view->getHelperPluginManager()->get(UrlHelper::class)->__invoke($name, $params, $options, $reuseMatchedParams);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return Url
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReuseQuery()
    {
        return $this->reuseQuery;
    }

    /**
     * @param bool $reuseQuery
     * @return Url
     */
    public function setReuseQuery($reuseQuery)
    {
        $this->reuseQuery = $reuseQuery;
        return $this;
    }
}
