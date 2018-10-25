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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sale\Session;

use CudiBundle\Entity\Sale\Session;
use LogicException;

/**
 * Add Sale Session Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Session|null
     */
    private $session;

    public function init()
    {
        if (null === $this->session) {
            throw new LogicException('Cannot edit the comment of a null sale session');
        }

        parent::init();

        $this->add(array(
            'type'    => 'textarea',
            'name'    => 'comment',
            'label'   => 'Comment',
            'value'   => $this->session->getComment(),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Edit Comment', 'sale_edit');
    }

    /**
     * @param  Session $session
     * @return self
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }
}
