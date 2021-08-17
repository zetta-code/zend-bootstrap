<?php

/**
 * @link      https://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

declare(strict_types=1);

namespace Zetta\ZendBootstrap\Form;

use Laminas\Filter;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class SearchForm extends Form
{
    /**
     * SearchForm constructor.
     * @inheritdoc
     */
    public function __construct($name = 'search', $options = [])
    {
        parent::__construct($name);
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');
        $this->setAttribute('novalidate', true);
        $this->setLabel(_('Search'));

        $this->add([
            'name' => 'q',
            'type' => Element\Text::class,
            'attributes' => [
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'name' => 'submit-btn',
            'type' => Element\Submit::class,
            'attributes' => [
                'class' => 'btn btn-outline-secondary',
                'value' => '<i class="fa fa-search"></i>',
                'id' => $name . '-submit',
            ],
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'q',
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
            'validators' => [
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 256,
                    ],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }
}
