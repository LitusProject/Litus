<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace CommonBundle\Component\Form\Bootstrap\Decorator;

use CommonBundle\Component\Form\Bootstrap\Element\Tabs,
    Zend\Form\Decorator\HtmlTag;

/**
 * Zend_Form_Decorator_FormElements
 *
 * Render all form tabs registered with current form
 *
 * Accepts following options:
 * - separator: Separator to use between elements
 *
 * Any other options passed will be used as HTML attributes of the form tag.
 *
 * @uses       \Zend\Form\Form
 * @uses       \Zend\Form\Decorator\AbstractDecorator
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormTabs extends \Zend\Form\Decorator\AbstractDecorator
{
    /**
     * Render form elements
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Tabs) {
            return $content;
        }
        
        $name      = $element->getName();
        $attribs   = $element->getAttribs();
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $name;
        }
        if (!array_key_exists('class', $attribs)) {
            $attribs['class'] = 'nav nav-pills';
        }
        if (!array_key_exists('tag', $attribs)) {
            $attribs['tag'] = 'ul';
        }
        unset($attribs['helper']);
        
        foreach($element->getTabs() as $label => $href) {
            $tab = new HtmlTag();
            $tab->setOptions(
                array(
                    'tag' => 'li',
                )
            );
            
            $link = new HtmlTag();
            $link->setOptions(
                array(
                    'tag' => 'a',
                    'href'  => $href,
                    'data-toggle' => 'tab',
                )
            );
            
            if (null !== ($translator = $element->getTranslator())) {
                $label = $translator->translate($label);
            }
            
            $content .= $tab->render($link->render($label));
        }
        
        $container = new HtmlTag();
        $container->setOptions($attribs);
        
        return $container->render($content);
    }
}
