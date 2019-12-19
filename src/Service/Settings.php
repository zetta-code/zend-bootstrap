<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Service;

use InvalidArgumentException;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;

class Settings
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $defaultFilename;

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
    protected $defaultConfig;

    /**
     * Configure constructor.
     * @param string $filename
     * @param bool $override
     */
    public function __construct($filename, $override = false, $defaultFilename=null)
    {
        $this->filename = $filename;
        $this->override = $override;
        $this->defaultFilename = $defaultFilename;
        $this->config = $this->getDefaultConfig()->merge($this->getConfig());
    }

    public function __destruct()
    {
        if ($this->override) {
            $writer = new PhpArray();
            $writer->setUseBracketArraySyntax(true);
            file_put_contents($this->filename, $writer->toString($this->getConfig()));
        }
    }

    /**
     * Get the Configure filename
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the Configure filename
     * @param string $filename
     * @return Settings
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get the Configure defaultFilename
     * @return string
     */
    public function getDefaultFilename()
    {
        return $this->defaultFilename;
    }

    /**
     * Set the Configure defaultFilename
     * @param string $defaultFilename
     * @return Settings
     */
    public function setDefaultFilename($defaultFilename)
    {
        $this->defaultFilename = $defaultFilename;
        return $this;
    }

    /**
     * Get the Settings override
     * @return bool
     */
    public function isOverride()
    {
        return $this->override;
    }

    /**
     * Set the Settings override
     * @param bool $override
     * @return Settings
     */
    public function setOverride($override)
    {
        $this->override = $override;
        return $this;
    }

    /**
     * Get the Configure config
     * @return Config
     */
    protected function getConfig()
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
     * Get the Configure defaultConfig
     * @return Config
     */
    protected function getDefaultConfig()
    {
        if ($this->defaultConfig === null) {
            if (file_exists($this->defaultFilename)) {
                $this->defaultFilename = realpath($this->defaultFilename);
                $this->defaultConfig = new Config(include $this->defaultFilename, true);
            } else {
                $this->defaultFilename = getcwd() . $this->defaultFilename;
                $this->defaultConfig = new Config([], true);
            }
        }
        return $this->defaultConfig;
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param array ...$name
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
     * @param string $name
     * @param mixed $value
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
}
