<?php

namespace LogisticsBundle\Form\Admin\PianoReservation;

/**
 * This form allows the user to edit the reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\PianoReservation\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'reservation_edit');
    }
}
