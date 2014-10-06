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

namespace CudiBundle\Form\Prof\Comment;

/**
 * Add Reply
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Reply extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'comment',
            'attributes' => array(
                'id' => 'comment',
            ),
            'options'    => array(
                'input' => array(
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'int'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'Textarea',
            'name'       => 'reply',
            'label'      => 'Reply',
            'required'   => true,
            'attributes' => array(
                'rows' => 5,
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }
}
