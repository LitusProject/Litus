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

use SyllabusBundle\Entity\Group;
use SyllabusBundle\Entity\Poc;
use SyllabusBundle\Repository\Group as GroupRepository;

/**
 * Add Poc
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Poc';

    /**
     * @var Group|null
     */
    protected $pocgroup = null;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'POC\'er',
            'required'   => true,
            'attributes' => array(
                'id'    => 'person',
                'style' => 'width: 400px;',
            ),
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'TypeaheadPerson'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'user_add');
    }

    /**
     * @param  Group $pocGroup
     * @return self
     */
    public function setPocgroup(Group $pocgroup)
    {
        $this->pocgroup = $pocgroup;

        return $this;
    }

    /**
     * @return poc
     */
    public function getPocgroup(Group $pocgroup)
    {
        return $this->pocgroup;
    }
}
