<?php

namespace BannerBundle\Form\Admin\Banner;

/**
 * Edit Banner
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \BannerBundle\Form\Admin\Banner\Add
{
    public function init($fileRequired = false)
    {
        parent::init($fileRequired);

        $this->remove('submit')
            ->addSubmit('Save', 'banner_edit');
    }
}
