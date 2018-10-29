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

namespace SyllabusBundle\Form\Admin\Study;

/**
 * Add Module Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroup extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->setLabel('Module Group');

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'module_group',
            'required'   => false,
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'TypeaheadStudyModuleGroup'),
                    ),
                ),
            ),
        ));
    }
}
