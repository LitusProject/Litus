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

namespace BrBundle\Form\Admin\Section;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    BrBundle\Entity\Contracts\Section,
    Doctrine\ORM\EntityManager,
    Zend\Form\Form,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea;

/**
 * Add a section.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('price');
        $field->setLabel('Price')
            ->setRequired()
            ->setValue('0')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('vat_type');
        $field->setLabel('VAT Type')
            ->setRequired()
            ->setMultiOptions($this->_getVatTypes($entityManager))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('invoice_description');
        $field->setLabel('Description on Invoice')
            ->setRequired(false)
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('content');
        $field->setLabel('Content')
            ->setRequired()
            ->setValue('<entry></entry>')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'contracts_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    /**
     * Retrieve the different VAT types applicable.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     */
    private function _getVatTypes(EntityManager $entityManager)
    {
        $types =  $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->findAllByPrefix(Section::VAT_CONFIG_PREFIX);

        $typesArray = array();
        foreach ($types as $type => $value)
            $typesArray[$type] = $value . '%';

        return $typesArray;
    }
}
