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
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea,
    Zend\Validator\Date as DateValidator;

/**
 * Add a event.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $opts The validator's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);
        
        $field = new Text('event_name');
        $field->setLabel('Event Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text('location');
        $field->setLabel('Location')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired()
            ->addValidator(new DateValidator('dd/MM/yyyy H:m'))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setRequired()
            ->addValidator(new DateValidator('dd/MM/yyyy H:m'))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Textarea('description');
        $field->setLabel('Description')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
                
        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'companies_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    public function populateFromEvent(Event $event)
    {
        $this->populate(
            array(
                'event_name' => $event->getName(),
                'location' => $event->getLocation(),
                'start_date' => $event->getStartDate()->format('d/m/Y H:i'),
                'end_date' => $event->getEndDate()->format('d/m/Y H:i'),
                'description' => $event->getDescription(),
            )
        );
    }
}
