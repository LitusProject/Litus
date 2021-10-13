<?php

namespace PublicationBundle\Form\Admin\Publication;

/**
 * This form allows the user to edit the Publication.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \PublicationBundle\Form\Admin\Publication\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'publication_edit');

        $this->bind($this->publication);
    }
}
