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

namespace ApiBundle\Form\Admin\Key;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    NewsBundle\Entity\Nodes\News,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Hostname as HostnameValidator;

/**
 * Add Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $opts The form's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);

        $field = new Text('host');
        $field->setLabel('Host')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new HostnameValidator());
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'key_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
