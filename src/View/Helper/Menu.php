<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\View\Helper;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;

class Menu extends \Zend\View\Helper\Navigation\Menu
{
    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer $container container to render
     * @param  string $ulClass CSS class for first UL
     * @param  string $indent initial indentation
     * @param  int|null $minDepth minimum depth
     * @param  int|null $maxDepth maximum depth
     * @param  bool $onlyActive render only active branch?
     * @param  bool $escapeLabels Whether or not to escape the labels
     * @param  bool $addClassToListItem Whether or not page class applied to <li> element
     * @param  string $liActiveClass
     * @return string
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
        if ($found) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
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
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth
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
                if ($ulClass && $depth == 0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else if ($page->getParent() != null && $ulClass = $page->getParent()->get('ulClass')) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }

            // render li tag and page
            $liClasses = [];
            // Is page active?
            if ($isActive) {
                $liClasses[] = 'active';
            }
            if ($wrapClass = $page->get('wrapClass')) {
                $liClasses[] = $wrapClass;
            }
            // Add CSS class from page to <li>
            //if ($addClassToListItem && $page->getClass()) {
            //    $liClasses[] = $page->getClass();
            //}
            $liClass = empty($liClasses) ? '' : ' class="' . implode(' ', $liClasses) . '"';

            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                . $myIndent . '        ' . $this->htmlify($page, $escapeLabels, $addClassToListItem) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i - 1);
                $html .= $myIndent . '    </li>' . self::EOL
                    . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }

        return $html;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param  AbstractPage $page page to generate HTML for
     * @param  bool $escapeLabel Whether or not to escape the label
     * @param  bool $addClassToListItem Whether or not to add the page class to the list item
     * @return string
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $addonLeft = $page->get('addon-left');
        $addonRight = $page->get('addon-right');
        $title = $page->getTitle();

        // translate label and title?
        if (null !== ($translator = $this->getTranslator())) {
            $textDomain = $this->getTranslatorTextDomain();
            if (is_string($label) && !empty($label)) {
                $label = $translator->translate($label, $textDomain);
            }
            if (is_string($title) && !empty($title)) {
                $title = $translator->translate($title, $textDomain);
            }
        }

        // get attribs for element
        $attribs = [
            'id' => $page->getId(),
            'title' => $title,
        ];

        // add additional attributes
        $attr = $page->get('attribs');
        if (is_array($attr)) {
            $attribs = $attribs + $attr;
        }

        //if ($addClassToListItem === false) {
        $attribs['class'] = $page->getClass();
        //}

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>';
        if ($addonLeft) {
            $html .= $addonLeft;
        }
        if ($escapeLabel === true) {
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }
        if ($addonRight) {
            $html .= $addonRight;
        }
        $html .= '</' . $element . '>';

        return $html;
    }
}
