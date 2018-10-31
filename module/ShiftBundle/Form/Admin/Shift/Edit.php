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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Form\Admin\Shift;

use ShiftBundle\Entity\Shift;

/**
 * Edit Shift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \ShiftBundle\Form\Admin\Shift\Add
{
    /**
     * @var Shift|null The shift to edit
     */
    private $shift;

    public function init()
    {
        parent::init();

        if (!$this->shift->canEditDates()) {
            $this->remove('start_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'start_date',
                    )
                );

            $this->remove('end_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'end_date',
                    )
                );
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
