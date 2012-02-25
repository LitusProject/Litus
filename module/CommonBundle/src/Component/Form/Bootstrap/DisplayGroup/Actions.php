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
 
namespace CommonBundle\Component\Form\Bootstrap\DisplayGroup;

use Zend\Loader\PrefixPathMapper;

/**
 * Reset form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Actions extends \Zend\Form\DisplayGroup
{
	
	/**
	 * Constructor
	 *
	 * @param  string $name
	 * @param  PrefixPathMapper $loader
	 * @param  array|Config $options
	 * @return void
	 */
	public function __construct($name, PrefixPathMapper $loader, $options = null)
	{
		parent::__construct($name, $loader, $options);
		
		$this->clearDecorators();
		$this->addDecorator('FormElements')
		     ->addDecorator('HtmlTag', array('class' => 'form-actions', 'tag' => 'div'));
	}
}
