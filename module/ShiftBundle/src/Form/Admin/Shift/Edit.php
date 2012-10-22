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

namespace ShiftBundle\Form\Admin\Shift;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    Shiftbundle\Entity\Shift,
    Zend\Form\Element\Text,
    Zend\Form\Element\Submit;

/**
 * Edit Shift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \ShiftBundle\Entity\Shift $shift The shift we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Shift $shift, $name = null)
    {
        parent::__construct($entityManager, $name);

        if ($shift->countResponsibles() != 0 || $shift->countVolunteers() != 0) {
            $field = new Hidden('start_date');
            $this->add($field);

            $field = new Hidden('end_date');
            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'shift_edit');
        $this->add($field);

        $this->_populateFromShift($shift);
    }

    private function _populateFromShift(Shift $shift)
    {
        $data = array(
            'manager_id' => $shift->getManager()->getId(),
            'start_date' => $shift->getStartDate()->format('d/m/Y H:i'),
            'end_date' => $shift->getEndDate()->format('d/m/Y H:i'),
            'manager' => $shift->getManager()->getFullName() . ' - ' . $shift->getManager()->getUniversityIdentification() ,
            'nb_responsibles' => $shift->getNbResponsibles(),
            'nb_volunteers' => $shift->getNbVolunteers(),
            'unit' => $shift->getUnit()->getId(),
            'event' => null === $shift->getEvent() ? '' : $shift->getEvent()->getId(),
            'location' => $shift->getLocation()->getId(),
            'name' => $shift->getName(),
            'description' => $shift->getDescription()
        );

        $this->setData($data);
    }
}
