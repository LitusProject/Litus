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

namespace BrBundle\Form\Admin\Event;

use LogicException;

/**
 * Edit an event.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Edit extends Add
{


    public function init()
    {

		if (null === $this->event) {
	        throw new LogicException('Cannot edit a null event');
	    }

        parent::init();

        $this->remove('event_add');

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'event_edit',
            'value'      => 'Edit',
            'attributes' => array(
                'class' => 'mail_add',
            ),
        ));
    }
}
