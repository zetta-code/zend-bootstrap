<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Filter;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class SettingsFilter extends InputFilter
{
    /**
     * SettingFilter constructor.
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $collection = new InputFilter();

        foreach ($settings as $name => $setting) {
            $collection->add($this->buildSetting($setting), $name);
        }

        $this->add($collection, 'settings');
    }

    /**
     * @param array $setting
     * @return InputFilter
     */
    public function buildSetting($setting)
    {
        $input = new InputFilter();
        $value = $setting['value'];
        $required = isset($setting['required']) ? (bool)$setting['required'] : true;
        $max = isset($setting['max']) ? $setting['max'] : 255;
        if (is_int($value)) {
            $input->add([
                'name' => 'value',
                'required' => $required,
                'filters' => [
                    ['name' => Filter\ToInt::class]
                ]
            ]);
        } elseif (is_bool($value)) {
            $input->add([
                'name' => 'value',
                'required' => $required,
                'filters' => [
                    [
                        'name' => Filter\Boolean::class,
                        'options' => [
                            'type' => [
                                Filter\Boolean::TYPE_ZERO_STRING
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'type' => [
                                'string', 'null'
                            ]
                        ]
                    ]
                ]
            ]);
        } else {
            $input->add([
                'name' => 'value',
                'required' => $required,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class]
                ],
                'validators' => [
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max' => $max
                        ]
                    ]
                ]
            ]);
        }

        return $input;
    }
}
