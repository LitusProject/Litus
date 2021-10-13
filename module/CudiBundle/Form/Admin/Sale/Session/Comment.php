<?php

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
        if ($this->session === null) {
            throw new LogicException('Cannot edit the comment of a null sale session');
        }

        parent::init();

        $this->add(
            array(
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
            )
        );

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
