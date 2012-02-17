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
 
namespace CudiBundle\Form\Admin\Sale;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\Price as PriceValidator,
	CudiBundle\Entity\Sales\Session,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Textarea;

class SessionComment extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(Session $session, $options = null)
    {
        parent::__construct($options);

        $field = new Textarea('comment');
        $field->setLabel('Comment');
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Edit Comment')
            ->setAttrib('class', 'sale_edit');
        $this->addElement($field);
        
        $this->populate(array('comment' => $session->getComment()));
    }
}
