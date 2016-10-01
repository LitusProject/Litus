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

namespace CommonBundle\Component\Form\Bootstrap\Note;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Submit form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Note extends \Zend\Form\Element implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        $this->addClass('form-control');
        $this->setLabelAttributes(
            array(
                'class' => 'col-sm-2 control-label',
            )
        );
    }
}
