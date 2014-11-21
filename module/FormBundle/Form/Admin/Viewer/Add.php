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

namespace FormBundle\Form\Admin\Viewer;

use CommonBundle\Component\Validator\Typeahead\Person as PersonTypeaheadValidator;

/**
 * Add Viewer
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'FormBundle\Hydrator\ViewerMap';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'typeahead',
            'name'     => 'person',
            'label'    => 'Person',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        new PersonTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'edit',
            'label' => 'Can Edit/Delete entries',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'mail',
            'label' => 'Can Mail Participants',
        ));

        $this->addSubmit('Add', 'add');
    }
}
