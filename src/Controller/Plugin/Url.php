<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Laminas\Http\Request;
use Laminas\Mvc\Controller\Plugin\Url as UrlPlugin;
use Laminas\Mvc\Exception;
use Laminas\Mvc\InjectApplicationEventInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ArrayUtils;

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
        if (isset($config['reuse_query']) && ! empty($config['reuse_query'])) {
            $this->setReuseQuery($config['reuse_query']);
        }
    }

    /**
     * @inheritdoc
     */
    public function fromRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        $controller = $this->getController();
        if (! $controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException(
                'Url plugin requires a controller that implements InjectApplicationEventInterface'
            );
        }

        $event = $controller->getEvent();
        $request = null;
        if ($event instanceof MvcEvent) {
            $request = $event->getRequest();
        }
        if (! $request instanceof Request) {
            throw new Exception\DomainException(
                'Url plugin requires that controller event compose a request; none found'
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
            $query = $request->getQuery()->toArray();
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
