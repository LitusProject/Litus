<?php

namespace LogisticsBundle\Form\Admin\VanReservation;

/**
 * This form allows the user to edit the reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\VanReservation\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'reservation_edit');
    }
}
