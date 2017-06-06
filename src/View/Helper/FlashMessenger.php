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
        $divOpen = '<div class="flashmessenger">';
        $divClose = '</div>';

        // default
        if ($this->getFlashMessengerHelper()->hasCurrentMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('default', array('alert', 'alert-success', 'animated', 'shake'));
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('default');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('default', array('alert', 'alert-success', 'animated', 'shake'));
        }

        // success
        if ($this->getFlashMessengerHelper()->hasCurrentSuccessMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('success', array('alert', 'alert-success', 'animated', 'shake'));
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('success');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('success', array('alert', 'alert-success', 'animated', 'shake'));
        }

        // info
        if ($this->getFlashMessengerHelper()->hasCurrentInfoMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('info', array('alert', 'alert-info', 'animated', 'shake'));
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('info');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('info', array('alert', 'alert-info', 'animated', 'shake'));
        }

        // warning
        if ($this->getFlashMessengerHelper()->hasCurrentWarningMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('warning', array('alert', 'alert-warning', 'animated', 'shake'));
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('warning');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('warning', array('alert', 'alert-warning', 'animated', 'shake'));
        }

        // error
        if ($this->getFlashMessengerHelper()->hasCurrentErrorMessages()) {
            $divOpen .= $this->getFlashMessengerHelper()->renderCurrent('error', array('alert', 'alert-danger', 'animated', 'shake'));
            $this->getFlashMessengerHelper()->clearCurrentMessagesFromNamespace('error');
        } else {
            $divOpen .= $this->getFlashMessengerHelper()->render('error', array('alert', 'alert-danger', 'animated', 'shake'));
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
