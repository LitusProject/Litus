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

namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Component\OldForm\Admin\Element\Textarea,
    CudiBundle\Entity\Sale\Session,
    Zend\Form\Element\Submit;

/**
 * Add Sale Session Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @param Session         $session
     * @param null|string|int $name    Optional name for the element
     */
    public function __construct(Session $session, $name = null)
    {
        parent::__construct($name);

        $field = new Textarea('comment');
        $field->setLabel('Comment');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Edit Comment')
            ->setAttribute('class', 'sale_edit');
        $this->add($field);

        $this->setData(
            array(
                'comment' => $session->getComment(),
            )
        );
    }
}
