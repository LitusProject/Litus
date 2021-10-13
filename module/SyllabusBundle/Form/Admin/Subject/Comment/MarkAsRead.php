<?php

namespace SyllabusBundle\Form\Admin\Subject\Comment;

use SyllabusBundle\Entity\Subject\Comment;

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
        parent::init();

        $this->setAttribute('class', '');

        $this->addSubmit(
            $this->comment->isRead() ? 'Mark As Unread' : 'Mark As Read',
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
