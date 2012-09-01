<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Component\Form\Admin\Element\Textarea,
    CudiBundle\Entity\Sales\Session,
    Zend\Form\Element\Submit;

/**
 * Add Sale Session Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \CudiBundle\Entity\Sales\Session $session
     * @param null|string|int $name Optional name for the element
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
