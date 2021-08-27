<?php

namespace GalleryBundle\Form\Admin\Album;

/**
 * Edit an album.
 */
class Edit extends \GalleryBundle\Form\Admin\Album\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'gallery_edit');
    }
}
