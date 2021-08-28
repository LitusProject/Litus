<?php

namespace CudiBundle\Form\Admin\Sale\Session\OpeningHour;

/**
 * Edit Opening Hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sale\Session\OpeningHour\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'clock_edit');
    }
}
