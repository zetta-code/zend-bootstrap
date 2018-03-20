<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2017 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\FlashMessenger as FlashMessengerHelper;

class FlashMessenger extends AbstractHelper
{
    /**
     * @var FlashMessengerHelper
     */
    protected $flashMessengerHelper;

    public function __invoke()
    {
        return $this->render();
    }

    public function render()
    {
        $divOpen = '<div class="flash-messenger">';
        $divClose = '</div>';

        // default
        if ($this->getFlashMessengerHelper()->hasCurrentMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('default', ['alert', 'alert-secondary', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('default');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('default', ['alert', 'alert-secondary', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
        }

        // success
        if ($this->getFlashMessengerHelper()->hasCurrentSuccessMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('success', ['alert', 'alert-success', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('success');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('success', ['alert', 'alert-success', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
        }

        // info
        if ($this->getFlashMessengerHelper()->hasCurrentInfoMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('info', ['alert', 'alert-info', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('info');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('info', ['alert', 'alert-info', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
        }

        // warning
        if ($this->getFlashMessengerHelper()->hasCurrentWarningMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('warning', ['alert', 'alert-warning', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('warning');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('warning', ['alert', 'alert-warning', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
        }

        // error
        if ($this->getFlashMessengerHelper()->hasCurrentErrorMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('error', ['alert', 'alert-danger', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('error');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('error', ['alert', 'alert-danger', 'alert-dismissible', 'fade', 'show', 'animated', 'shake']);
        }

        return $divOpen . '' . $divClose;
    }

    /**
     * Retrieve the FlashMessenger helper
     *
     * @return FlashMessengerHelper
     */
    protected function getFlashMessengerHelper()
    {
        if ($this->flashMessengerHelper) {
            return $this->flashMessengerHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->flashMessengerHelper = $this->view->plugin(FlashMessengerHelper::class);
        }

        if (!$this->flashMessengerHelper instanceof FlashMessengerHelper) {
            $this->flashMessengerHelper = new FlashMessengerHelper();
        }

        return $this->flashMessengerHelper;
    }
}
