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

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Nodes\Form,
    Zend\Form\Element\Submit;

/**
 * Edit Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Nodes\Form $form The notification we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Form $form, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'form_edit');
        $this->add($field);

        $this->_populateFromForm($form);
    }

    private function _populateFromForm(Form $form)
    {
        $data = array(
            'title'        => $form->getTitle(),
            'introduction' => $form->getIntroduction(),
            'submittext'   => $form->getSubmitText(),
            'start_date'   => $form->getStartDate()->format('d/m/Y H:i'),
            'end_date'     => $form->getEndDate()->format('d/m/Y H:i'),
            'active'       => $form->isActive(),
            'max'          => $form->getMax(),
            'multiple'     => $form->isMultiple(),
        );

        $this->setData($data);
    }
}
