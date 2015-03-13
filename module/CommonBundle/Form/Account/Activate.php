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

namespace CommonBundle\Form\Account;

/**
 * Account activate form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activate extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'password',
            'name'       => 'credential',
            'label'      => 'Password',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'password',
            'name'       => 'verify_credential',
            'label'      => 'Verify Password',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'identical',
                            'options' => array(
                                'token' => 'credential',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Activate');
    }
}
