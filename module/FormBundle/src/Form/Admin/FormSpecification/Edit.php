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

namespace FormBundle\Form\Admin\FormSpecification;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Nodes\FormSpecification,
    Zend\Form\Element\Submit;

/**
 * Edit FormSpecification
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Nodes\FormSpecification $formSpecification The notification we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, FormSpecification $formSpecification, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'form_edit');
        $this->add($field);

        $this->_populateFromForm($formSpecification);
    }

    private function _populateFromForm(FormSpecification $formSpecification)
    {
        $data = array(
            'title'        => $formSpecification->getTitle(),
            'introduction' => $formSpecification->getIntroduction(),
            'submittext'   => $formSpecification->getSubmitText(),
            'start_date'   => $formSpecification->getStartDate()->format('d/m/Y H:i'),
            'end_date'     => $formSpecification->getEndDate()->format('d/m/Y H:i'),
            'active'       => $formSpecification->isActive(),
            'max'          => $formSpecification->getMax(),
            'multiple'     => $formSpecification->isMultiple(),
        );

        $this->setData($data);
    }
}
