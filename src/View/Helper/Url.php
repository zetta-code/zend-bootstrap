<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Traversable;
use Zend\Http\Request;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url as UrlHelper;

class Url extends AbstractHelper
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlHelper
     */
    protected $url;

    /**
     * @var bool
     */
    protected $reuseQuery = false;

    /**
     * Referer constructor.
     * @param Request $request
     * @param UrlHelper $url
     * @param array $config
     */
    public function __construct(Request $request, UrlHelper $url, $config = [])
    {
        $this->request = $request;
        $this->url = $url;

        if (isset($config['reuse_query']) && !empty($config['reuse_query'])) {
            $this->setReuseQuery($config['reuse_query']);
        }
    }

    /**
     * @param string $name Name of the route
     * @param array $params Parameters for the link
     * @param array|Traversable $options Options for the route
     * @param bool $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                         For the link href attribute
     */
    public function __invoke($name = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        if (null === $this->request) {
            throw new Exception\RuntimeException('No Request instance provided');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = [];
        }

        if (isset($options['reuse_queried_params'])) {
            $reuseQueriedParams = (bool)$options['reuse_queried_params'];
        } else {
            $reuseQueriedParams = $this->isReuseQuery();
        }
        if ($reuseQueriedParams) {
            $query = $this->request->getQuery()->toArray();
            if (count($query) > 0) {
                $options = ArrayUtils::merge(['query' => $query], $options);
            }
        }

        return $this->url->__invoke($name, $params, $options, $reuseMatchedParams);
    }

    /**
     * Get the Url reuseQuery
     * @return bool
     */
    public function isReuseQuery()
    {
        return $this->reuseQuery;
    }

    /**
     * Set the Url reuseQuery
     * @param bool $reuseQuery
     * @return Url
     */
    public function setReuseQuery($reuseQuery)
    {
        $this->reuseQuery = $reuseQuery;
        return $this;
    }
}
