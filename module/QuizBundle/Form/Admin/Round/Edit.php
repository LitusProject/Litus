<?php

namespace QuizBundle\Form\Admin\Round;

/**
 * Edits a quiz round
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends \QuizBundle\Form\Admin\Round\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Edit', 'edit');

        $this->bind($this->round);
    }
}
