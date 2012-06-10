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
 
namespace CommonBundle\Form\Admin\Config;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Entity\General\Config,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Textarea;

/**
 * Edit Configuration
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
	/**
	 * @param \CommonBundle\Entity\Public\Config $entry The configuration entry we are editing
	 * @param mixed $opts The form's options
	 */
    public function __construct(Config $entry, $opts = null)
    {
        parent::__construct($opts);
		
		$field = new Text('key');
		$field->setLabel('Key')
			->setAttrib('disabled', 'disabled')
		    ->setDecorators(array(new FieldDecorator()));
		$this->addElement($field);
		
		if (strlen($entry->getValue()) > 40) {
	        $field = new Textarea('value');
	        $field->setLabel('Value')
	            ->setRequired()
	            ->setDecorators(array(new FieldDecorator()));
	        $this->addElement($field);
	    } else {
	    	$field = new Text('value');
	    	$field->setLabel('Value')
	    	    ->setRequired()
	    	    ->setDecorators(array(new FieldDecorator()));
	    	$this->addElement($field);
	    }

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'config_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
		
        $this->populate(
            array(
            	'key' => $entry->getKey(),
                'value' => $entry->getValue()
            )
        );
    }
}
