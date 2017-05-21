<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Hydrator\Strategy;

use Jenssegers\Date\Date;
use Zend\Hydrator\Strategy\StrategyInterface;

class DateStrategy implements StrategyInterface
{
    /**
     * @var \IntlDateFormatter
     */
    protected $formatter;

    public function __construct()
    {
        $this->formatter = new \IntlDateFormatter(null, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
    }

    public function extract($value)
    {
        if ($value != null) {
            return $value->format($this->getDateFormat());
        }

        return $value;
    }

    private function getDateFormat()
    {

        $patterns = array(
            '/11\D21\D(1999|99)/',
            '/21\D11\D(1999|99)/',
            '/(1999|99)\D11\D21/',
        );
        $replacements = array('m/d/Y', 'd/m/Y', 'Y/m/d');

        $date = new \DateTime();
        $date->setDate(1999, 11, 21);
        return preg_replace($patterns, $replacements, $this->formatter->format($date));
    }

    public function hydrate($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        if (is_string($value)) {
            return Date::createFromFormat($this->getDateFormat() . ' H:i', $value . ' 00:00');
        }

        return $value;
    }
}
