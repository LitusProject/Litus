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
 
namespace CudiBundle\Form\Admin\Discount;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\Price as PriceValidator,
	CudiBundle\Component\Validator\Discount as DiscountValidator,
	CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Select,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;

/**
 * Add Discount
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;
    
    public function __construct(Article $article, EntityManager $entityManager, $options = null)
    {
        parent::__construct($options);
        
        $this->_entityManager = $entityManager;
             
        $field = new Text('value');
        $field->setLabel('Value')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
        
        $field = new Select('method');
        $field->setLabel('Method')
            ->setMultiOptions(array('percentage' => 'Percentage', 'fixed' => 'Fixed'))
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Select('type');
        $field->setLabel('Type')
           	->setRequired()
        	->setMultiOptions($this->_getTypes())
            ->setDecorators(array(new FieldDecorator()))
        	->addValidator(new DiscountValidator($article, $entityManager));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'discount_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    private function _getTypes()
    {
    	$types = $this->_entityManager
        	->getRepository('CudiBundle\Entity\Articles\Discount\Type')
    		->findAll();
    	$typeOptions = array();
    	foreach($types as $type)
    		$typeOptions[$type->getId()] = $type->getName();
    	
    	return $typeOptions;
    }
}