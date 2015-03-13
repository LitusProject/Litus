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

namespace CommonBundle\Form\Admin\Unit;

use LogicException;

/**
 * Edit Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        if (null === $this->unit) {
            throw new LogicException('Cannot edit a null unit');
        }

        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'unit_edit');
    }
}
