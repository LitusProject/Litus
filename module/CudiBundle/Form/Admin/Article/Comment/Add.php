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

namespace CudiBundle\Form\Admin\Article\Comment;

use CudiBundle\Entity\Comment\Comment;

/**
 * Add Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Comment\Comment';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'textarea',
            'name'     => 'text',
            'label'    => 'Comment',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The comment type defines the visibility of the comment:
                <ul>
                    <li><b>Internal:</b> These comments will only be visible in the admin</li>
                    <li><b>External:</b> These comments will only be visible in \'Prof App\' and in the admin</li>
                    <li><b>Site:</b> These comments will also be visible on the website</li>
                </ul>',
                'options'   => Comment::$possibleTypes,
            ),
        ));

        $this->addSubmit('Add', 'comment_add');
    }
}
