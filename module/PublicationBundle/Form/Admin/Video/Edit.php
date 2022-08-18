<?php

namespace PublicationBundle\Form\Admin\Video;

/**
 * This form allows the user to edit the Video.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Edit extends \PublicationBundle\Form\Admin\Video\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'video_edit');

        $this->bind($this->video);
    }
}
