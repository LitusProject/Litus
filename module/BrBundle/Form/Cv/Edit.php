<?php

namespace BrBundle\Form\Cv;

use BrBundle\Entity\Cv\Entry as CvEntry;

/**
 * Edit Cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \BrBundle\Form\Cv\Add
{
    /**
     * @var CvEntry
     */
    private $entry;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes');

        if ($this->entry !== null) {
            $this->bind($this->entry);
        }
    }

    /**
     * @param  CvEntry $entry
     * @return self
     */
    public function setEntry(CvEntry $entry)
    {
        $this->entry = $entry;

        return $this;
    }
}
