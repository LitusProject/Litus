<?php

namespace SyllabusBundle\Form\Admin\Subject;

/**
 * Edit Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SyllabusBundle\Form\Admin\Subject\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->subject);
    }
}
