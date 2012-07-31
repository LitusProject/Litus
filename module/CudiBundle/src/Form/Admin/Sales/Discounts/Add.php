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
 
namespace CudiBundle\Form\Admin\Sales\Discounts;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CudiBundle\Component\Validator\Discount as DiscountValidator,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Hidden,
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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;
    
    public function __construct(Article $article, EntityManager $entityManager, $options = null)
    {
        parent::__construct($options);
        
        $this->_entityManager = $entityManager;

        $field = new Select('template');
        $field->setAttrib('id', 'discount_template')
            ->setLabel('Template')
            ->setMultiOptions($this->_getTemplates())
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $templates = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Discounts\Template')
            ->findAll();
        foreach($templates as $template) {
            $field = new Hidden('template_' . $template->getId() . '_value');
            $field->setAttrib('id', 'template_' . $template->getId() . '_value')
                ->setValue(number_format($template->getValue()/100, 2))
                ->clearDecorators()
                ->setDecorators(array('ViewHelper'));
            $this->addElement($field);
            
            $field = new Hidden('template_' . $template->getId() . '_method');
            $field->setAttrib('id', 'template_' . $template->getId() . '_method')
                ->setValue($template->getMethod())
                ->clearDecorators()
                ->setDecorators(array('ViewHelper'));
            $this->addElement($field);
            
            $field = new Hidden('template_' . $template->getId() . '_type');
            $field->setAttrib('id', 'template_' . $template->getId() . '_type')
                ->setValue($template->getType())
                ->clearDecorators()
                ->setDecorators(array('ViewHelper'));
            $this->addElement($field);
        }
                     
        $field = new Text('value');
        $field->setAttrib('id', 'discount_template_value')
            ->setLabel('Value')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new PriceValidator());
        $this->addElement($field);
        
        $field = new Select('method');
        $field->setAttrib('id', 'discount_template_method')
            ->setLabel('Method')
            ->setMultiOptions(array('percentage' => 'Percentage', 'fixed' => 'Fixed', 'override' => 'Override'))
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Select('type');
        $field->setAttrib('id', 'discount_template_type')
            ->setLabel('Type')
               ->setRequired()
            ->setMultiOptions(array('member' => 'Member', 'acco' => 'Acco'))
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new DiscountValidator($article, $entityManager));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'discount_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    private function _getTemplates()
    {
        $templates = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Discounts\Template')
            ->findAll();
        $templateOptions = array(0 => 'none');
        foreach($templates as $template)
            $templateOptions[$template->getId()] = $template->getName();
        
        return $templateOptions;
    }
    
    public function isValid($data)
    {
        if ($data['template'] != 0) {
            $validatorsInternal = array();
            $requiredInternal = array();
            
            $validatorsInternal['value'] = $this->getElement('value')->getValidators();
            $requiredInternal['value'] = $this->getElement('value')->isRequired();
            $this->getElement('value')->clearValidators()
                ->setRequired(false);
                
            $validatorsInternal['method'] = $this->getElement('method')->getValidators();
            $requiredInternal['method'] = $this->getElement('method')->isRequired();
            $this->getElement('method')->clearValidators()
                ->setRequired(false);
            
            $validatorsInternal['type'] = $this->getElement('type')->getValidators();
            $requiredInternal['type'] = $this->getElement('type')->isRequired();
            $this->getElement('type')->clearValidators()
                ->setRequired(false);
        }
        
        $isValid = parent::isValid($data);
        
        if ($data['template'] != 0) {
            $this->getElement('value')->setValidators($validatorsInternal['value'])
                ->setRequired($requiredInternal['value']);
            $this->getElement('method')->setValidators($validatorsInternal['method'])
                ->setRequired($requiredInternal['method']);
            $this->getElement('type')->setValidators($validatorsInternal['type'])
                ->setRequired($requiredInternal['type']);
        }
        
        return $isValid;
    }
}
