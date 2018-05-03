<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
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
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config['reuse_query']) && !empty($config['reuse_query'])) {
            $this->setReuseQuery($config['reuse_query']);
        }
    }

    /**
     * @inheritdoc
     */
    public function fromRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException(
                'Url plugin requires a controller that implements InjectApplicationEventInterface'
            );
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
            $query = $controller->getRequest()->getQuery()->toArray();
            if (count($query) > 0) {
                $options = ArrayUtils::merge(['query' => $query], $options);
            }
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
