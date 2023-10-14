<?php

namespace ShiftBundle\Form\Admin\RegistrationShift;

/**
 * Add multiple registrations at once; only made to get the hydrator in OpeningHourController
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Schedule extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShiftBundle\Hydrator\RegistrationShift';
}

