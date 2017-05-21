<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Hydrator;

use Zetta\ZendBootstrap\Hydrator\Strategy\DateTimeStrategy;

class ClassMethods extends \Zend\Hydrator\ClassMethods
{
    /**
     * @param bool|true $underscoreSeparatedKeys
     */
    public function __construct($underscoreSeparatedKeys = true)
    {
        parent::__construct($underscoreSeparatedKeys);

        $dateTimeStrategy = new DateTimeStrategy();
        $this->strategies['createdAt'] = $dateTimeStrategy;
        $this->strategies['updatedAt'] = $dateTimeStrategy;
    }
}
