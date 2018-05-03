<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Hydrator\Strategy;

use Jenssegers\Date\Date;

class DateTimeStrategy extends DateStrategy
{
    /**
     * @var string
     */
    protected $time = 'H:i';

    /**
     * DateTimeStrategy constructor.
     * @param string $time
     */
    public function __construct($time = 'H:i')
    {
        parent::__construct();
        $this->time = $time;
    }

    /**
     * Get the DateTimeStrategy time
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set the DateTimeStrategy time
     * @param string $time
     * @return DateTimeStrategy
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract($value)
    {
        if ($value != null) {
            return $value->format($this->getDateFormat());
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function hydrate($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        if (is_string($value)) {
            return Date::createFromFormat($this->getDateFormat(), $value);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
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
        return preg_replace($patterns, $replacements, $this->formatter->format($date)) . ' ' . $this->time;
    }
}
