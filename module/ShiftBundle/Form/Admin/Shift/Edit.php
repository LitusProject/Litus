<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Form\Admin\Shift;

use CommonBundle\Entity\User\Person\Academic,
    LogicException,
    Shiftbundle\Entity\Shift;

/**
 * Edit Shift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Shift|null The shift to edit.
     */
    private $shift;

    public function init()
    {
        if (null === $this->shift) {
            throw new LogicException('Cannot edit a null shift');
        }

        parent::init();

        if (!$this->shift->canEditDates()) {
            $this->remove('start_date')
                ->add(array(
                    'type' => 'hidden',
                    'name' => 'start_date',
                ));

            $this->remove('end_date')
                ->add(array(
                    'type' => 'hidden',
                    'name' => 'end_date',
                ))
        }

        $this->remove('submit')
            ->addSubmit('Save', 'shift_edit');

        $this->bind($this->shift);
    }

    /**
     * @param  Shift $shift
     * @return self
     */
    public function setShift(Shift $shift)
    {
        $this->shift = $shift;

        return $this;
    }
}
