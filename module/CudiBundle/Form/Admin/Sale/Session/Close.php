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

namespace CudiBundle\Form\Admin\Sale\Session;

use LogicException;

/**
 * Close Sale Session
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Close extends Add
{
    public function init()
    {
        if (null === $this->cashRegister) {
            throw new LogicException('Cannot close sale session with a null cash register');
        }

        parent::init();

        $this->remove('submit')
            ->addSubmit('Close', 'sale_edit');

        $this->setData(
            $this->getHydrator()
                ->extract($this->cashRegister)
        );
    }
}
