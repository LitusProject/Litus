<?php

namespace QuizBundle\Form\Admin\Tiebreaker;

/**
 * Edits a quiz team
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends \QuizBundle\Form\Admin\Tiebreaker\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Edit', 'edit');

        $this->bind($this->tiebreaker);
    }
}
