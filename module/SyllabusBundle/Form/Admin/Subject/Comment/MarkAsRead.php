<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Form\Admin\Subject\Comment;

use LogicException,
    SyllabusBundle\Entity\Subject\Comment;

/**
 * Mark Comment As Read
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MarkAsRead extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Comment
     */
    private $comment;

    public function init()
    {
        if (null === $this->comment) {
            throw new LogicException('No comment given to mark as read');
        }

        parent::init();

        $this->setAttribute('class', '');

        $this->addSubmit(
            $this->comment->isRead() ? 'Mark As Unread' : 'Mark As Read'.
            'sign',
            'mark_as_read'
        );
    }

    /**
     * @param  Comment $comment
     * @return self
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
