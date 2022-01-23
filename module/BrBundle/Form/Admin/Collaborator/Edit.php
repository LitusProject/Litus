<?php

namespace BrBundle\Form\Admin\Collaborator;

/**
 * Edit an collaborator.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Admin\Collaborator\Add
{
    public function init()
    {
        parent::init();

        $this->remove('person');

        $this->remove('submit')
            ->addSubmit('Save', 'collaborator_edit');
    }
}
