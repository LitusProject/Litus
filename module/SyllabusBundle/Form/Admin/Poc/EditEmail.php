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

namespace SyllabusBundle\Form\Admin\Poc;

use SyllabusBundle\Entity\Poc;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EditEmail extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Poc';

    /**
     * @var Group|null
     */
    protected $poc = null;

    public function init()
    {
        if (null === $this->poc) {
            throw new LogicException('Cannot edit null poc');
        }

        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'emailAdress',
            'label'      => 'Email adress of this years poc',
            'required'   => true,
            'attributes' => array(
                'size'  => 70,
                'value' => $this->poc->getEmailAdress(),

            ),)
            );
        $this->addSubmit('Save', 'edit');
    }
    public function setPoc(Poc $poc)
    {
        $this->poc = $poc;

        return $this;
    }
}
