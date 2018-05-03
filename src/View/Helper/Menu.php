<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View\Exception;
use Zend\View\Helper\Navigation\Menu as ZendMenu;

class Menu extends ZendMenu
{
    /**
     * default CSS class to use for li elements
     *
     * @var string
     */
    protected $defaultLiClass = 'nav-item';

    /**
     * CSS class to use for the ul sub-menu element
     *
     * @var string
     */
    protected $subUlClass = 'dropdown-menu';

    /**
     * CSS class to use for the 1. level (NOT root level!) ul sub-menu element
     *
     * @var string
     */
    protected $subUlClassLevel1 = 'dropdown-menu';

    /**
     * CSS class to use for the active li sub-menu element
     *
     * @var string
     */
    protected $subLiClassLevel0 = 'dropdown';

    /**
     * CSS class to use for the active li sub-menu element
     *
     * @var string
     */
    protected $subLiClassLevelN = 'dropdown-submenu';

    /**
     * CSS class to use for the active li sub-menu element
     *
     * @var string
     */
    protected $subAClassLevel0 = 'dropdown-toggle';

    /**
     * CSS class to use for the active li sub-menu element
     *
     * @var string
     */
    protected $subAClassLevelN = 'dropdown-submenu-toggle';


    /**
     * HREF string to use for the sub-menu toggle element's HREF attribute,
     * to override current page's href/'htmlify' setting
     *
     * @var string
     */
    protected $hrefSubToggleOverride = null;

    /**
     * Partial view script to use for rendering menu link/item
     *
     * @var array
     */
    protected $pagePartials = [];

    /**
     * @inheritdoc
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    )
    {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);

        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );

        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $page->set('depth', $depth);
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible)
                            || is_int($maxDepth) && $foundDepth + 1 > $maxDepth
                        ) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }
                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);
            if ($depth > $prevDepth) {
                // start new ul tag
                $ulClass = '' .
                    ($depth == 0 ? $this->getUlClass() :
                        ($depth == 1 ? $this->getSubUlClassLevel1() : $this->getSubUlClass())
                    );
                if (strlen($ulClass) > 0) {
                    $ulClass = ' class="' . $escaper($ulClass) . '"';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses = [];

            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }
            if (!empty($this->getDefaultLiClass())) {
                $liClasses[] = $this->getDefaultLiClass();
            }
            $isBelowMaxLevel = ($maxDepth > $depth) || ($maxDepth === null) || ($maxDepth === false);
            if (!empty($page->pages) && $isBelowMaxLevel) {
                $liClasses[] = ($depth == 0 ? $this->getSubLiClassLevel0() : $this->getSubLiClassLevelN());
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            if ($page->get('liClass')) {
                $liClasses[] = $page->get('liClass');
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';
            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL
                . $myIndent . '        ' . $this->htmlify($page, $escapeLabels, $addClassToListItem) . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i - 1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                    . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        $partial = $this->getPagePartial(get_class($page));
        if ($partial) {
            return $this->htmlifyWithPartial($page, $escapeLabel, $addClassToListItem, $partial);
        }

        // get attribs for element
        $attribs = [
            'id' => $page->getId(),
            'title' => $this->translate($page->getTitle(), $page->getTextDomain()),
        ];

        $classnames = [];
        if ($addClassToListItem === false) {
            $class = $page->getClass();
            if (!empty($class)) {
                $classnames[] = $page->getClass();
            }
        }
        $maxDepth = $this->getMaxDepth();
        $depth = $page->get('depth');
        $isBelowMaxLevel = ($maxDepth > $depth) || ($maxDepth === null) || ($maxDepth === false);
        if (count($page->getPages()) > 0 && $isBelowMaxLevel) {
            $classnames[] = $depth == 0 ? $this->getSubAClassLevel0() : $this->getSubAClassLevelN();
            $attribs['data-toggle'] = 'dropdown';
        }
        $attribs['class'] = implode(" ", $classnames);

        // add additional attributes
        $attr = $page->get('attribs');
        if (is_array($attr)) {
            $attribs = $attribs + $attr;
        }

        // does page have a href?
        if (count($page->getPages()) > 0 && !empty($this->getHrefSubToggleOverride())) {
            $href = $this->getHrefSubToggleOverride();
        } else {
            $href = $page->getHref();
        }
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>';
        $addonLeft = $page->get('addon-left');
        if ($addonLeft) {
            $html .= $addonLeft;
        }
        $label = $this->translate($page->getLabel(), $page->getTextDomain());
        if ($escapeLabel === true) {
            /** @var \Zend\View\Helper\EscapeHtml $escaper */
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }
        $addonRight = $page->get('addon-right');
        if ($addonRight) {
            $html .= $addonRight;
        }
        $html .= '</' . $element . '>';

        return $html;
    }

    /**
     * Renders the given $page by invoking the partial view helper
     *
     * The container will simply be passed on as a model to the view script
     * as-is, and will be available in the partial script as 'container', e.g.
     * <code>echo 'Number of pages: ', count($this->container);</code>.
     *
     * @param  AbstractPage $page page to generate HTML for
     * @param  bool $escapeLabel Whether or not to escape the label
     * @param  bool $addClassToListItem Whether or not to add the page class to the list item
     * @param  string|array $partial [optional] partial view script to use.
     *                                    Default is to use the partial
     *                                    registered in the helper. If an array
     *                                    is given, it is expected to contain two
     *                                    values; the partial view script to use,
     *                                    and the module where the script can be
     *                                    found.
     * @return string
     * @throws Exception\RuntimeException if no partial provided
     * @throws Exception\InvalidArgumentException if partial is invalid array
     */
    public function htmlifyWithPartial(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false, $partial = null)
    {
        if (null === $partial) {
            $partial = $this->getPartial();
        }

        if (empty($partial)) {
            throw new Exception\RuntimeException(
                'Unable to render menu: No partial view script provided'
            );
        }

        $model = [
            'page' => $page,
            'escapeLabel' => $escapeLabel,
            'addClassToListItem' => $addClassToListItem,
            'menu' => (clone $this),

        ];

        /** @var \Zend\View\Helper\Partial $partialHelper */
        $partialHelper = $this->view->plugin('partial');
        if (is_array($partial)) {
            if (count($partial) != 2) {
                throw new Exception\InvalidArgumentException(
                    'Unable to render menu: A view partial supplied as '
                    . 'an array must contain two values: partial view '
                    . 'script and module where script can be found'
                );
            }

            return $partialHelper($partial[0], $model);
        }

        return $partialHelper($partial, $model);
    }

    /**
     * Get the Menu defaultLiClass
     * @return string
     */
    public function getDefaultLiClass()
    {
        return $this->defaultLiClass;
    }

    /**
     * Set the Menu defaultLiClass
     * @param string $defaultLiClass
     * @return Menu
     */
    public function setDefaultLiClass($defaultLiClass)
    {
        $this->defaultLiClass = $defaultLiClass;
        return $this;
    }

    /**
     * Get the Menu subUlClass
     * @return string
     */
    public function getSubUlClass()
    {
        return $this->subUlClass;
    }

    /**
     * Set the Menu subUlClass
     * @param string $subUlClass
     * @return Menu
     */
    public function setSubUlClass($subUlClass)
    {
        $this->subUlClass = $subUlClass;
        return $this;
    }

    /**
     * Get the Menu subUlClassLevel1
     * @return string
     */
    public function getSubUlClassLevel1()
    {
        return $this->subUlClassLevel1;
    }

    /**
     * Set the Menu subUlClassLevel1
     * @param string $subUlClassLevel1
     * @return Menu
     */
    public function setSubUlClassLevel1($subUlClassLevel1)
    {
        $this->subUlClassLevel1 = $subUlClassLevel1;
        return $this;
    }

    /**
     * Get the Menu subLiClassLevel0
     * @return string
     */
    public function getSubLiClassLevel0()
    {
        return $this->subLiClassLevel0;
    }

    /**
     * Set the Menu subLiClassLevel0
     * @param string $subLiClassLevel0
     * @return Menu
     */
    public function setSubLiClassLevel0($subLiClassLevel0)
    {
        $this->subLiClassLevel0 = $subLiClassLevel0;
        return $this;
    }

    /**
     * Get the Menu subLiClassLevelN
     * @return string
     */
    public function getSubLiClassLevelN()
    {
        return $this->subLiClassLevelN;
    }

    /**
     * Set the Menu subLiClassLevelN
     * @param string $subLiClassLevelN
     * @return Menu
     */
    public function setSubLiClassLevelN($subLiClassLevelN)
    {
        $this->subLiClassLevelN = $subLiClassLevelN;
        return $this;
    }

    /**
     * Get the Menu subAClassLevel0
     * @return string
     */
    public function getSubAClassLevel0()
    {
        return $this->subAClassLevel0;
    }

    /**
     * Set the Menu subAClassLevel0
     * @param string $subAClassLevel0
     * @return Menu
     */
    public function setSubAClassLevel0($subAClassLevel0)
    {
        $this->subAClassLevel0 = $subAClassLevel0;
        return $this;
    }

    /**
     * Get the Menu subAClassLevelN
     * @return string
     */
    public function getSubAClassLevelN()
    {
        return $this->subAClassLevelN;
    }

    /**
     * Set the Menu subAClassLevelN
     * @param string $subAClassLevelN
     * @return Menu
     */
    public function setSubAClassLevelN($subAClassLevelN)
    {
        $this->subAClassLevelN = $subAClassLevelN;
        return $this;
    }

    /**
     * Get the Menu hrefSubToggleOverride
     * @return string
     */
    public function getHrefSubToggleOverride()
    {
        return $this->hrefSubToggleOverride;
    }

    /**
     * Set the Menu hrefSubToggleOverride
     * @param string $hrefSubToggleOverride
     * @return Menu
     */
    public function setHrefSubToggleOverride($hrefSubToggleOverride)
    {
        $this->hrefSubToggleOverride = $hrefSubToggleOverride;
        return $this;
    }

    /**
     * Sets which partial view script to use for rendering menu
     * @param    string $page
     * @param    string|array $partial partial view script or null. If an array is
     *                                given, it is expected to contain two
     *                                values; the partial view script to use,
     *                                and the module where the script can be
     *                                found.
     * @return Menu
     */
    public function putPagePartial($page, $partial)
    {
        if (null === $partial || is_string($partial) || is_array($partial)) {
            $this->pagePartials[$page] = $partial;
        }

        return $this;
    }

    /**
     * Returns partial view script to use for rendering menu
     * @param string $page
     * @return string|array|null
     */
    public function getPagePartial($page)
    {
        if (isset($this->pagePartials[$page])) {
            return $this->pagePartials[$page];
        }

        return null;
    }
}
