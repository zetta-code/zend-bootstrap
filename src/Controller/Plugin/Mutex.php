<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Mutex extends AbstractPlugin
{
    protected $dir = './data/lock/';
    protected $files = []; // resources to lock
    protected $owns = [];

    /**
     * Mutex constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if (isset($config['dir']) && !empty($config['dir'])) {
            $this->dir = $config['dir'];
        }

        if (!file_exists($this->dir)) {
            mkdir($this->dir, 0777, true);
        }
    }

    /**
     * Ceate a new resource or get exisitng with same key
     * @param $key
     */
    protected function init($key)
    {
        if (!isset($this->files[$key])) {
            $this->files[$key] = fopen($this->dir . $key . '.lockfile', 'w+');
            $this->owns[$key] = false;
        }
    }

    /**
     * Lock resource
     * @param $key
     * @return bool
     */
    public function lock($key)
    {
        $this->init($key);

        if (!flock($this->files[$key], LOCK_EX)) { //failed
            error_log('ExclusiveLock::acquire_lock FAILED to acquire lock [' . $key . ']');
            return false;
        } else {
            ftruncate($this->files[$key], 0); // truncate file
            // write something to just help debugging
            fwrite($this->files[$key], 'Locked' . PHP_EOL);
            fflush($this->files[$key]);

            $this->owns[$key] = true;
            return true;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function unlock($key)
    {
        if (isset($this->owns[$key]) && $this->owns[$key] === true) {
            if (!flock($this->files[$key], LOCK_UN)) { //failed
                error_log('ExclusiveLock::lock FAILED to release lock [' . $key . ']');
                return false;
            }
            ftruncate($this->files[$key], 0); // truncate file
            // write something to just help debugging
            fwrite($this->files[$key], 'Unlocked' . PHP_EOL);
            fflush($this->files[$key]);
            $this->owns[$key] = false;
            return true;
        } else {
            error_log('ExclusiveLock::unlock called on [' . $key . '] but its not acquired by caller');
            return false;
        }
    }
}
