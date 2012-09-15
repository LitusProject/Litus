<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Field;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Field,
    Zend\Form\Element\Submit;

/**
 * Edit Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Nodes\Field $field The field we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Field $field, EntityManager $entityManager, $name = null)
    {
        parent::__construct($field->getForm(), $entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'field_edit');
        $this->add($field);

        $this->_populateFromField($field);
    }

    private function _populateFromField(Field $field)
    {
        $data = array(
            'label'    => $field->getLabel(),
            'order'    => $field->getOrder(),
            'required' => $field->isRequired(),
        );

        $this->setData($data);
    }
}
