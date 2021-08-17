<?php

/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Hydrator\Strategy;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use IntlDateFormatter;
use Laminas\Hydrator\Strategy\Exception;
use Laminas\Hydrator\Strategy\StrategyInterface;

class CarbonDateTimeStrategy implements StrategyInterface
{
    const DEFAULT_TIME_FORMAT = 'H:i';

    /**
     * @var IntlDateFormatter
     */
    protected $formatter;

    /**
     * @var string|null
     */
    protected $dateFormat = null;

    /**
     * @var string|null
     */
    protected $timeFormat = null;

    /**
     * @var bool
     */
    protected $startOfDay = true;

    /**
     * CarbonDateTimeStrategy constructor.
     *
     * @param string|null $dateFormat
     * @param string|null $timeFormat
     * @param bool $startOfDay
     */
    public function __construct(?string $dateFormat = null, ?string $timeFormat = null, bool $startOfDay = true)
    {
        $this->formatter = new IntlDateFormatter(null, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
        $this->dateFormat = $dateFormat;
        $this->setTimeFormat($timeFormat);
        $this->startOfDay = $startOfDay;
    }

    /**
     * Get the CarbonDateTimeStrategy formatter.
     *
     * @return IntlDateFormatter
     */
    public function getFormatter(): IntlDateFormatter
    {
        return $this->formatter;
    }

    /**
     * Set the CarbonDateTimeStrategy formatter.
     *
     * @param IntlDateFormatter $formatter
     *
     * @return CarbonDateTimeStrategy
     */
    public function setFormatter(IntlDateFormatter $formatter): CarbonDateTimeStrategy
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Get the CarbonDateTimeStrategy dateFormat.
     *
     * @return string|null
     */
    public function getDateFormat(): ?string
    {
        if ($this->dateFormat === null) {
            $this->dateFormat = $this->deduceDateFormat();
        }
        return $this->dateFormat;
    }

    /**
     * Set the CarbonDateTimeStrategy dateFormat.
     *
     * @param string|null $dateFormat
     *
     * @return CarbonDateTimeStrategy
     */
    public function setDateFormat(?string $dateFormat): CarbonDateTimeStrategy
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * Get the CarbonDateTimeStrategy timeFormat.
     *
     * @return string|null
     */
    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    /**
     * Set the CarbonDateTimeStrategy timeFormat.
     *
     * @param string|null $timeFormat
     * @param string $glue
     *
     * @return CarbonDateTimeStrategy
     */
    public function setTimeFormat(?string $timeFormat, string $glue = ' '): CarbonDateTimeStrategy
    {
        if ($timeFormat !== null) {
            $timeFormat = $glue . $timeFormat;
        }
        $this->timeFormat = $timeFormat;
        return $this;
    }

    /**
     * Get the CarbonDateTimeStrategy startOfDay.
     *
     * @return bool
     */
    public function isStartOfDay(): bool
    {
        return $this->startOfDay;
    }

    /**
     * Set the CarbonDateTimeStrategy startOfDay.
     *
     * @param bool $startOfDay
     *
     * @return CarbonDateTimeStrategy
     */
    public function setStartOfDay(bool $startOfDay): CarbonDateTimeStrategy
    {
        $this->startOfDay = $startOfDay;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract($value, ?object $object = null)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format($this->getDateFormat());
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function hydrate($value, ?array $data)
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

        $hydrated = Carbon::createFromFormat($this->getDateTimeFormat(), $value);
        return $hydrated ? ($this->startOfDay ? $hydrated->startOfDay() : $hydrated) : $value;
    }

    /**
     * Set the CarbonDateTimeStrategy time to default.
     *
     * @return CarbonDateTimeStrategy
     */
    public function setDefaultTimeFormat(): CarbonDateTimeStrategy
    {
        $this->timeFormat = self::DEFAULT_TIME_FORMAT;
        return $this;
    }

    /**
     * @return string
     */
    protected function deduceDateFormat(): string
    {

        $patterns = [
            '/11\D21\D(1999|99)/',
            '/21\D11\D(1999|99)/',
            '/(1999|99)\D11\D21/',
        ];
        $replacements = ['m/d/Y', 'd/m/Y', 'Y/m/d'];

        try {
            $date = new DateTime();
            $date->setDate(1999, 11, 21);
            return preg_replace($patterns, $replacements, $this->formatter->format($date));
        } catch (\Exception $exception) {
            return 'm/d/Y';
        }
    }

    /**
     * @return string
     */
    protected function getDateTimeFormat(): string
    {
        return $this->timeFormat !== null ? $this->getDateFormat() . $this->timeFormat : $this->getDateFormat();
    }
}
