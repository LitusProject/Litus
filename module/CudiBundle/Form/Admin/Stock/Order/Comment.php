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

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Entity\Stock\Order;
use LogicException;

/**
 * Add Order Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Order|null
     */
    private $order;

    public function init()
    {
        if ($this->order === null) {
            throw new LogicException('Cannot comment on a null order.');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'comment',
                'label'      => 'Comment',
                'required'   => true,
                'value'      => $this->order->getComment(),
                'attributes' => array(
                    'style' => 'height: 50px;',
                ),
                'options' => array(
                    'input' => array(
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'edit', 'save');
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
