<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;
use Zend\Stdlib\ErrorHandler;
use Zetta\ZendBootstrap\Service\Thumbnail;

class ToThumbnail extends AbstractFilter
{
    /**
     * @var array
     */
    protected $options = [
        'overwrite' => true
    ];

    /**
     * Store already filtered values, so we can filter multiple
     * times the same file without being block by move_uploaded_file
     * internal checks
     *
     * @var array
     */
    protected $alreadyFiltered = [];

    /**
     * @var Thumbnail
     */
    protected $thumbnail;

    /**
     * Constructor
     *
     * @param array|string $options The target file path or an options array
     */
    public function __construct($options = [])
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * @param  bool $flag Shall existing files be overwritten?
     * @return self
     */
    public function setOverwrite($flag = true)
    {
        $this->options['overwrite'] = (bool)$flag;
        return $this;
    }

    /**
     * @return Thumbnail
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param $thumbnail
     * @return ToThumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        // An uploaded file? Retrieve the 'tmp_name'
        $isFileUpload = false;
        if (is_array($value)) {
            if (!isset($value['tmp_name'])) {
                return $value;
            }

            $isFileUpload = true;
            $uploadData = $value;
            $sourceFile = $value['tmp_name'];
        } else {
            $uploadData = [
                'tmp_name' => $value,
                'name' => $value,
            ];
            $sourceFile = $value;
        }

        if (isset($this->alreadyFiltered[$sourceFile])) {
            return $this->alreadyFiltered[$sourceFile];
        }

        $info = pathinfo($sourceFile);
        $targetFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.png';

        $this->checkFileExists($targetFile);
        $this->moveFile($sourceFile, $targetFile);
        $this->thumbnail->process($targetFile, $targetFile);

        $return = $targetFile;
        if ($isFileUpload) {
            $return = $uploadData;
            $return['tmp_name'] = $targetFile;
        }

        $this->alreadyFiltered[$sourceFile] = $return;

        return $return;
    }

    /**
     * @param  string $targetFile Target file path
     * @throws Exception\InvalidArgumentException
     */
    protected function checkFileExists($targetFile)
    {
        if (file_exists($targetFile)) {
            if ($this->getOverwrite()) {
                unlink($targetFile);
            } else {
                throw new Exception\InvalidArgumentException(
                    sprintf("File '%s' could not be renamed. It already exists.", $targetFile)
                );
            }
        }
    }

    /**
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->options['overwrite'];
    }

    /**
     * @param  string $sourceFile Source file path
     * @param  string $targetFile Target file path
     * @throws Exception\RuntimeException
     * @return bool
     */
    protected function moveFile($sourceFile, $targetFile)
    {
        ErrorHandler::start();
        $result = rename($sourceFile, $targetFile);
        $warningException = ErrorHandler::stop();
        if (!$result || null !== $warningException) {
            throw new Exception\RuntimeException(
                sprintf("File '%s' could not be renamed. An error occurred while processing the file.", $sourceFile),
                0,
                $warningException
            );
        }

        return $result;
    }
}
