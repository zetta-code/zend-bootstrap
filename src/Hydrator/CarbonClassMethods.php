<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Hydrator;

use Zetta\ZendBootstrap\Hydrator\Strategy\CarbonDateTimeStrategy;

class CarbonClassMethods extends \Zend\Hydrator\ClassMethods
{
    /**
     * @inheritDoc
     */
    public function __construct($underscoreSeparatedKeys = true, $methodExistsCheck = false)
    {
        parent::__construct($underscoreSeparatedKeys, $methodExistsCheck);

        $dateTimeStrategy = new CarbonDateTimeStrategy();
        $this->strategies['createdAt'] = $dateTimeStrategy;
        $this->strategies['updatedAt'] = $dateTimeStrategy;
    }
}
