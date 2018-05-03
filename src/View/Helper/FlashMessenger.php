<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger as PluginFlashMessenger;
use Zend\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger as FlashMessengerHelper;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Helper\AbstractHelper;

class FlashMessenger extends AbstractHelper
{
    /**
     * @var FlashMessengerHelper
     */
    protected $flashMessengerHelper;

    /**
     * Default class for the div tag
     *
     * @var string
     */
    protected $divClass = 'alerts';

    /**
     * Default attributes for the open format tag
     *
     * @var array
     */
    protected $classes = [
        PluginFlashMessenger::NAMESPACE_DEFAULT => ['alert', 'alert-secondary', 'alert-dismissible', 'fade', 'show', 'animated', 'shake'],
        PluginFlashMessenger::NAMESPACE_SUCCESS => ['alert', 'alert-success', 'alert-dismissible', 'fade', 'show', 'animated', 'shake'],
        PluginFlashMessenger::NAMESPACE_INFO => ['alert', 'alert-info', 'alert-dismissible', 'fade', 'show', 'animated', 'shake'],
        PluginFlashMessenger::NAMESPACE_WARNING => ['alert', 'alert-warning', 'alert-dismissible', 'fade', 'show', 'animated', 'shake'],
        PluginFlashMessenger::NAMESPACE_ERROR => ['alert', 'alert-danger', 'alert-dismissible', 'fade', 'show', 'animated', 'shake'],
    ];

    /**
     * @return FlashMessenger
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render All Messages
     *
     * @param  array $classes
     * @param  null|bool $autoEscape
     * @return string
     */
    public function render(array $classes = [], $autoEscape = null)
    {
        $divOpen = '<div class="' . $this->divClass . '">';
        $divClose = '</div>';
        $hasMessages = false;

        foreach ($this->classes as $namespace => $namespaceClasses) {
            $namespaceClasses = ArrayUtils::merge($namespaceClasses, $classes);
            if ($this->getFlashMessengerHelper()->getPluginFlashMessenger()->hasCurrentMessages($namespace)) {
                $hasMessages = true;
                $divOpen .= $this->getFlashMessengerHelper()->renderCurrent($namespace, $namespaceClasses, $autoEscape);
                $this->getFlashMessengerHelper()->getPluginFlashMessenger()->clearCurrentMessagesFromNamespace($namespace);
            } elseif ($this->getFlashMessengerHelper()->getPluginFlashMessenger()->hasMessages($namespace)) {
                $hasMessages = true;
                $divOpen .= $this->getFlashMessengerHelper()->render($namespace, $namespaceClasses, $autoEscape);
            }
        }

        return $hasMessages ? $divOpen . $divClose : '';
    }

    /**
     * Get the FlashMessenger flashMessengerHelper
     * @return FlashMessengerHelper
     */
    public function getFlashMessengerHelper()
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

    /**
     * Set the FlashMessenger flashMessengerHelper
     * @param FlashMessengerHelper $flashMessengerHelper
     * @return FlashMessenger
     */
    public function setFlashMessengerHelper($flashMessengerHelper)
    {
        $this->flashMessengerHelper = $flashMessengerHelper;
        return $this;
    }

    /**
     * Get the FlashMessenger divClass
     * @return string
     */
    public function getDivClass()
    {
        return $this->divClass;
    }

    /**
     * Set the FlashMessenger divClass
     * @param string $divClass
     * @return FlashMessenger
     */
    public function setDivClass($divClass)
    {
        $this->divClass = $divClass;
        return $this;
    }

    /**
     * Get the FlashMessenger classes
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Set the FlashMessenger classes
     * @param array $classes
     * @return FlashMessenger
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }
}
