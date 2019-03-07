<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Form\View\Helper;

use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\MonthSelect;
use Zend\Form\ElementInterface;
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormRow as FormRowHelper;

class FormRow extends FormRowHelper
{
    /**
     * @inheritdoc
     */
    protected $inputErrorClass = 'is-invalid';

    /**
     * The attributes for the row errors
     *
     * @var array
     */
    protected $elementErrorsAttributes = [
        'class' => 'invalid-feedback'
    ];

    /**
     * @param ElementInterface $element
     * @param null $labelPosition
     * @return string
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();

        if (is_null($labelPosition)) {
            $labelPosition = $this->labelPosition;
        }

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        // Does this element have errors ?
        if (count($element->getMessages()) > 0 && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if ($this->partial) {
            $vars = [
                'element' => $element,
                'label' => $label,
                'labelAttributes' => $this->labelAttributes,
                'labelPosition' => $labelPosition,
                'renderErrors' => $this->renderErrors,
            ];

            return $this->view->render($this->partial, $vars);
        }

        if ($this->renderErrors) {
            if (!empty($element->getOption('input_error_class'))) {
                $this->setInputErrorClass($element->getOption('input_error_class'));
            }

            if (!empty($element->getOption('element_errors_attributes'))) {
                $elementErrorsAttributes = $element->getOption('element_errors_attributes');
            } else {
                $elementErrorsAttributes = $this->getElementErrorsAttributes();
            }

            $elementErrors = $elementErrorsHelper
                ->setMessageOpenFormat('<div%s><div>')
                ->setMessageSeparatorString('</div><div>')
                ->setMessageCloseString('</div></div>')
                ->render($element, $elementErrorsAttributes);
        }

        $divOption = $element->getOption('div');
        if (!empty($divOption)) {
            $divClass = isset($divOption['class']) ? $divOption['class'] : '';
            $divErrorClass = isset($divOption['class_error']) ? $divOption['class_error'] : '';
            if (count($element->getMessages()) > 0 && !empty($divErrorClass)) {
                $divClass = !empty($divClass) ? $divClass . ' ' : '';
                $divClass = $divClass . $divErrorClass;
            }
            if (!empty($divClass)) {
                $div = sprintf('<div class="%s">', $divClass) . '%s</div>';
            } else {
                $div = '<div>%s</div>';
            }
        } else {
            $div = '%s';
        }

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');
        if ($type === 'multi_checkbox'
            || $type === 'radio') {
            $this->getView()->plugin('form' . str_replace('_', '', $type))->setSeparator('</div><div class="' . $divClass . '">');
        }
        $elementString = $elementHelper->render($element);

        if (isset($label) && '' !== $label && $type !== 'hidden') {
            $labelAttributes = [];

            if ($element instanceof LabelAwareInterface) {
                $labelAttributes = $element->getLabelAttributes();
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            if ($type === 'multi_checkbox'
                || $type === 'radio'
                || $element instanceof MonthSelect
                || $element instanceof Captcha
            ) {
                $markup = sprintf(
                    '<div class="form-group"><label>%s</label><div' . (count($element->getMessages()) > 0 ? ' class="form-check-is-invalid"' : '') . '>%s%s</div></div>',
                    $label,
                    sprintf($div, $elementString),
                    $elementErrors
                );
            } else {
                $horizontalClass = $element->getOption('horizontal_class');
                if (!empty($horizontalClass)) {
                    $elementString = '<div class="' . $horizontalClass . '">' . $elementString . $elementErrors . '</div>';
                    $elementErrors = '';
                }

                // Ensure element and label will be separated if element has an `id`-attribute.
                // If element has label option `always_wrap` it will be nested in any case.
                if ($element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap')) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
                } else {
                    $labelOpen = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }

                if ($label !== ''
                    && ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
                ) {
                    if ($element->getLabelOption('span_class')) {
                        $label = '<span class="' . $element->getLabelOption('span_class') . '">' . $label;
                    } else {
                        $label = '<span>' . $label;
                    }
                    $label .= '</span>';
                }

                // Button element is a special case, because label is always rendered inside it
                if ($element instanceof Button) {
                    $labelOpen = $labelClose = $label = '';
                }

                if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position')) {
                    $labelPosition = $element->getLabelOption('label_position');
                }

                switch ($labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $labelOpen . $label . $elementString . $labelClose . $elementErrors;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $labelOpen . $elementString . $label . $labelClose . $elementErrors;
                        break;
                }

                $markup = sprintf($div, $markup);
            }
        } else {
            $markup = sprintf($div, $elementString);
        }

        return $markup;
    }

    /**
     * Set the attributes for the row errors
     *
     * @param array $elementErrorsAttributes
     * @return FormRow
     */
    public function setElementErrorsAttributes($elementErrorsAttributes)
    {
        $this->elementErrorsAttributes = $elementErrorsAttributes;
        return $this;
    }

    /**
     * Get the attributes for the row $erros
     *
     * @return array
     */
    public function getElementErrorsAttributes()
    {
        return $this->elementErrorsAttributes;
    }
}
