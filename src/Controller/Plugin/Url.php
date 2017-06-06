<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2017 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\Url as UrlPlugin;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\ArrayUtils;

class Url extends UrlPlugin
{
    /**
     * @var bool
     */
    protected $reuseQuery = false;

    /**
     * Url constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if (isset($config['reuse-query']) && !empty($config['reuse-query'])) {
            $this->setReuseQuery($config['reuse-query']);
        }
    }

    public function fromRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false, $reuseQueriedParams = null)
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException(
                'Url plugin requires a controller that implements InjectApplicationEventInterface'
            );
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
        $query = $controller->getRequest()->getQuery()->toArray();
        if ($reuseQueriedParams && $query) {
            $options = ArrayUtils::merge(['query' => $query], $options);
        }

        return parent::fromRoute($route, $params, $options, $reuseMatchedParams);
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
