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

namespace SecretaryBundle\Form\Admin\WorkingGroup;

class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SecretaryBundle\Hydrator\WorkingGroup\Academic';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'Account',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'user_add');
    }
}
