<?php

namespace BrBundle\Form\Admin\Company\Job;

/**
 * Edit Job
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Admin\Company\Job\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'company_edit');
    }
}
