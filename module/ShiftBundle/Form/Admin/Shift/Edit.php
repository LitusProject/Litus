<?php

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
