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
 
namespace BrBundle\Form\Admin\Company\Event;

use BrBundle\Entity\Company\Event,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit an event.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param mixed $opts The validator's options
     */
    public function __construct(Event $event, $opts = null)
    {
        parent::__construct($opts);
        
        $this->removeElement('submit');
        
        $field = new Submit('submit');
        $field->setLabel('Edit')
            ->setAttrib('class', 'companies_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromEvent($event);
    }
}
