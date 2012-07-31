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
 
namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Supplier,
    Zend\Form\Element\Submit;

/**
 * Edit Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Supplier\Add
{
    public function __construct(Supplier $supplier, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');
        
        $field = new Submit('submit');
        $field->setLabel('Save')
                ->setAttrib('class', 'supplier_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromSupplier($supplier);
    }
}
