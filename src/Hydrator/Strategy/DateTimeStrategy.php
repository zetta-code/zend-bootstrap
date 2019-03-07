<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Hydrator\Strategy;

use DateTime;
use DateTimeInterface;
use Jenssegers\Date\Date;
use Zend\Hydrator\Strategy\Exception;

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
        if ($value instanceof DateTimeInterface) {
            return $value->format($this->getDateFormat());
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function hydrate($value)
    {
        if ($value === '' || $value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        if (! is_string($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Unable to hydrate. Expected null, string, or DateTimeInterface; %s was given.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $hydrated =  Date::createFromFormat($this->getDateFormat(), $value);

        return $hydrated ?: $value;
    }

    /**
     * @inheritdoc
     */
    private function getDateFormat()
    {
        $patterns = [
            '/11\D21\D(1999|99)/',
            '/21\D11\D(1999|99)/',
            '/(1999|99)\D11\D21/',
        ];
        $replacements = ['m/d/Y', 'd/m/Y', 'Y/m/d'];

        $date = new DateTime();
        $date->setDate(1999, 11, 21);
        return preg_replace($patterns, $replacements, $this->formatter->format($date)) . ' ' . $this->time;
    }
}
