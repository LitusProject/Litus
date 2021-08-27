<?php

namespace BrBundle\Form\Corporate\Job;

use BrBundle\Entity\Company\Job;

/**
 * Edit Job
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Corporate\Job\Add
{
    /**
     * @var Job
     */
    private $job;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes');

        if ($this->job !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->job));
        }
    }

    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }
}
