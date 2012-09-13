<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NotificationBundle\Form\Admin\Notification;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    NotificationBundle\Entity\Nodes\Notification,
    Zend\Form\Element\Submit;

/**
 * Edit Notification
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \NotificationBundle\Entity\Nodes\Notification $notification The notification we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Notification $notification, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'notification_edit');
        $this->add($field);

        $this->_populateFromNotification($notification);
    }

    private function _populateFromNotification(Notification $notification)
    {
        $data = array(
            'content'    => $notification->getContent(),
            'start_date' => $notification->getStartDate()->format('d/m/Y H:i'),
            'end_date'   => $notification->getEndDate()->format('d/m/Y H:i'),
            'active'     => $notification->getActive(),
        );

        $this->setData($data);
    }
}
