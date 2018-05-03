<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Zend\Mvc\Controller\Plugin\Params;
use Zend\Paginator\Paginator as ZendPaginator;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\PaginationControl;

class Paginator extends AbstractHelper
{
    /**
     * @var Params
     */
    protected $params;

    /**
     * Paginator constructor.
     * @param Params $params
     */
    public function __construct(Params $params)
    {
        $this->params = $params;
    }

    /**
     * @return Params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param Params $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    public function __invoke(ZendPaginator $paginator = null, $scrollingStyle = 'sliding', $partial = 'partial/paginator', $params = null)
    {
        if (count($paginator) !== 0) {
            /** @var PaginationControl $paginationControl */
            $paginationControl = $this->view->plugin('paginationControl');

            if ($params === null) {
                $params = [];
            }

            if (!isset($params['route'])) {
                $params['route'] = null;
            }
            if (!isset($params['params'])) {
                $params['params'] = [];
            }
            $params['options']['query'] = $this->params->fromQuery();
            if (!isset($params['reuseMatchedParams'])) {
                $params['reuseMatchedParams'] = true;
            }

            return $paginationControl->__invoke(
                $paginator,
                $scrollingStyle,
                $partial,
                $params
            );
        }

        return '';
    }
}
