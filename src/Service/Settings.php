<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Service;

use InvalidArgumentException;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;

class Settings
{
    /**
     * @var string|null
     */
    protected $filename;

    /**
     * @var string|null
     */
    protected $filenameDefault;

    /**
     * @var bool
     */
    protected $override = false;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Config
     */
    protected $default;

    /**
     * Configure constructor.
     * @param string $filename
     * @param string|null $filenameDefault
     * @param bool $override
     */
    public function __construct(string $filename, ?string $filenameDefault = null, bool $override = false)
    {
        $this->filename = $filename;
        $this->filenameDefault = $filenameDefault;
        $this->override = $override;
        $this->merge();
    }

    public function __destruct()
    {
        if ($this->override) {
            $this->save();
        }
    }

    /**
     * Get the Settings filename.
     *
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set the Settings filename.
     *
     * @param string|null $filename
     *
     * @return Settings
     */
    public function setFilename(?string $filename): Settings
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get the Settings filenameDefault.
     *
     * @return string|null
     */
    public function getFilenameDefault(): ?string
    {
        return $this->filenameDefault;
    }

    /**
     * Set the Settings filenameDefault.
     *
     * @param string|null $filenameDefault
     *
     * @return Settings
     */
    public function setFilenameDefault(?string $filenameDefault): Settings
    {
        $this->filenameDefault = $filenameDefault;
        return $this;
    }

    /**
     * Get the Settings override.
     *
     * @return bool
     */
    public function isOverride(): bool
    {
        return $this->override;
    }

    /**
     * Set the Settings override.
     *
     * @param bool $override
     *
     * @return Settings
     */
    public function setOverride(bool $override): Settings
    {
        $this->override = $override;
        return $this;
    }

    /**
     * Get the Settings config.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        if ($this->config === null) {
            if (file_exists($this->filename)) {
                $this->filename = realpath($this->filename);
                $this->config = new Config(include $this->filename, true);
            } else {
                $this->filename = getcwd() . $this->filename;
                $this->config = new Config([], true);
            }
        }

        return $this->config;
    }

    /**
     * Get the Settings default.
     *
     * @return Config
     */
    public function getDefault(): Config
    {
        if ($this->default === null) {
            if (file_exists($this->filenameDefault)) {
                $this->filenameDefault = realpath($this->filenameDefault);
                $this->default = new Config(include $this->filenameDefault, true);
            } elseif (file_exists($this->filename . '.dist')) {
                $this->filenameDefault = realpath($this->filename . '.dist');
                $this->default = new Config(include $this->filenameDefault, true);
            } else {
                $this->filenameDefault = getcwd() . $this->filenameDefault;
                $this->default = new Config([], true);
            }
        }

        return $this->default;
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param array $names
     *
     * @return mixed
     */
    public function get(...$names)
    {
        if (count($names) === 1 && is_array($names[0])) {
            $names = $names[0];
        }
        if (count($names) >= 1) {
            $setting = $this->getConfig();
            foreach ($names as $name) {
                $setting = $setting->get($name);
            }
            return $setting;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @param array $names
     * @return bool
     */
    public function has(...$names)
    {
        if (count($names) === 1 && is_array($names[0])) {
            $names = $names[0];
        }
        if (count($names) >= 1) {
            $setting = $this->getConfig();
            foreach ($names as $name) {
                if (!$setting->offsetExists($name)) {
                    return false;
                }
                $setting = $setting->get($name);
            }
            return true;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @param array $names
     *
     * @return Settings
     */
    public function put(...$names)
    {
        if (count($names) >= 2) {
            $value = array_pop($names);
            $name = array_pop($names);
            if (count($names) > 0) {
                $this->get($names)->offsetSet($name, $value);
            } else {
                $this->getConfig()->offsetSet($name, $value);
            }
            return $this;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return bool
     */
    public function save() {
        $writer = new PhpArray();
        $writer->setUseBracketArraySyntax(true);
        return is_int(file_put_contents($this->filename, $writer->toString($this->getConfig())));
    }

    /**
     * @return Settings
     */
    public function merge() {
        $default = clone $this->getDefault();
        $config = $this->getConfig();
        $config->setReadOnly();
        $this->config = $default->merge($config);
        return $this;
    }
}
