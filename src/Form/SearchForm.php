<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2017 Zetta Code
 */

namespace Zetta\ZendBootstrap\Form;

use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class SearchForm extends Form
{
    public function __construct()
    {
        parent::__construct('search');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');
        $this->setLabel('Search');

        $this->add([
            'name' => 'q',
            'type' => Element\Text::class,
            'attributes' => [
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'class' => 'btn btn-default',
                'value' => '<i class="fa fa-search"></i>',
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
