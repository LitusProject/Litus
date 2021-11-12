<?php

namespace NotificationBundle\Form\Admin\Notification;

/**
 * Edit Notification
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \NotificationBundle\Form\Admin\Notification\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'notification_edit');
    }
}
