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

use SyllabusBundle\Entity\Subject\Comment,
    Zend\Form\Element\Submit;

/**
 * Mark Comment As Read
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MarkAsRead extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Comment $comment, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', '');

        $field = new Submit('mark_as_read');
        $field->setValue($comment->isRead() ? 'Mark As Unread': 'Mark As Read')
            ->setAttribute('class', 'sign');
        $this->add($field);
    }
}
