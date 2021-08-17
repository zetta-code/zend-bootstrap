<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Zetta\ZendBootstrap\Filter\SettingsFilter;
use Zetta\ZendBootstrap\Form\Fieldset\SettingFieldset;

class SettingsForm extends Form
{

    /**
     * SettingForm constructor.
     * @param array $settings
     * @param string $name
     * @param array $options
     */
    public function __construct($settings, $name = 'settings', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setBindOnValidate(false);
        $this->setInputFilter(new SettingsFilter($settings['settings']));

        $setting = new SettingFieldset('setting', ['parent' => $name]);

        $this->add([
            'name' => 'settings',
            'type' => Element\Collection::class,
            'options' => [
                // https://zendframework.github.io/zend-form/element/collection/
                'should_create_template' => false,
                'allow_add' => true,
                'count' => 0,
                'target_element' => $setting,
            ]
        ]);

        $this->add([
            'name' => 'submit-btn',
            'type' => 'Submit',
            'attributes' => [
                'class' => 'btn btn-lg btn-block btn-primary',
                'value' => _('Save'),
                'id' => $name . '-submit',
            ],
        ]);

        $this->setData($settings);
    }
}
