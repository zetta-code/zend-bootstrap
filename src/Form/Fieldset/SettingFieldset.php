<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Form\Fieldset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class SettingFieldset extends Fieldset
{
    protected $builded = false;

    /**
     * @var string
     */
    protected $parent;

    /**
     * SettingFieldset constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'setting', $options = [])
    {
        parent::__construct($name, $options);

        $this->parent = isset($options['parent']) ? $options['parent'] : 'settings';
    }

    public function populateValues($data)
    {
        if (! $this->builded) {
            $placeholder = isset($data['name']) ? $data['name'] : _('Value');
            $value_options = isset($data['value_options']) ? $data['value_options'] : null;
            $label = $placeholder;

            if (is_array($value_options)) {
                $this->add([
                    'name' => 'value',
                    'type' => Element\Select::class,
                    'attributes' => [
                        'class' => 'form-control selectpicker',
                        'title' => _('Select'),
                        'data-container' => 'body',
                        'data-live-search' => 'true',
                    ],
                    'options' => [
                        'label' => $label,
                        'div' => ['class' => 'form-group'],
                        'value_options' => $value_options,
                    ],
                ]);
            } elseif (is_bool($data['value'])) {
                $data['value'] = $data['value'] ? '1' : '0';
                $this->add([
                    'name' => 'value',
                    'type' => Element\Radio::class,
                    'options' => [
                        'label' => $label,
                        'div' => ['class' => 'form-group'],
                        'value_options' => [
                            [
                                'value' => '0',
                                'label' => _('No'),
                                'attributes' => [
                                    'id' => $this->parent . '_' . $this->getName() . '_value_0',
                                    'onchange' => 'toggleYesNoButtonClass($(this).attr(\'id\'));'
                                ],
                                'label_attributes' => [
                                    'class' => 'btn btn-no btn-outline-secondary',
                                ],
                            ],
                            [
                                'value' => '1',
                                'label' => _('Yes'),
                                'attributes' => [
                                    'id' => $this->parent . '_' . $this->getName() . '_value_1',
                                    'onchange' => 'toggleYesNoButtonClass($(this).attr(\'id\'));'
                                ],
                                'label_attributes' => [
                                    'class' => 'btn btn-yes btn-outline-secondary',
                                ],
                            ],
                        ],
                    ],
                ]);
            } else {
                $this->add([
                    'name' => 'value',
                    'type' => Element\Text::class,
                    'attributes' => [
                        'class' => 'form-control',
                        'placeholder' => $placeholder,
                    ],
                    'options' => [
                        'label' => $label,
                        'div' => ['class' => 'form-group', 'class_error' => 'has-error']
                    ],
                ]);
            }
            $this->builded = true;
        }

        parent::populateValues($data);
    }
}
