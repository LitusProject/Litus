<?php

namespace QuizBundle\Form\Admin\Quiz;

/**
 * Edits a quiz
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends \QuizBundle\Form\Admin\Quiz\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Edit', 'edit');
    }
}
