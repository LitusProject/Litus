<?php

namespace SyllabusBundle\Form\Admin\Group;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SyllabusBundle\Form\Admin\Group\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->group);
    }
}
