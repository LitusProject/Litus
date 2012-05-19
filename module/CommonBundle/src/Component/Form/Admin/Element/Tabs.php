<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Form\Bootstrap\Element;

use \CommonBundle\Component\Form\Bootstrap\Decorator\FormTabs as FormTabsHelper;

/**
 * Tabs
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Tabs extends \Zend\Form\Element\Xhtml
{
    private $tabs = array();

    /**
     * Create new Tabs
     *
     * @param  string|array|Config $spec
     * @param  array|Traversable $options
     * @return void
     * @throws ElementException if no element name after initialization
     */
    public function __construct($spec, $options = null)
    {
    	parent::__construct($spec, $options);
    	$this->setDecorators(
    	    array(
    	        new FormTabsHelper()
    	    )
    	);
    }
    
    /**
     * @param array $tabs
     *
     * @return \CommonBundle\Component\Form\Bootstrap\Element\Tabs
     */
    public function setTabs($tabs = array())
    {
        $this->tabs = $tabs;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getTabs()
    {
        return $this->tabs;
    }
    
    /**
     * @param array $tab
     *
     * @return \CommonBundle\Component\Form\Bootstrap\Element\Tabs
     */
    public function addTab($tab)
    {
        $this->tabs = array_merge($this->tabs, $tab);
        return $this;
    }
}
