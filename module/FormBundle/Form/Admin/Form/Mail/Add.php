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

namespace FormBundle\Form\Admin\Form\Mail;


use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language;

/**
 * Add mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    public function init()
    {
        $this->add(array(
            'type'     => 'text',
            'name'     => 'from',
            'label'    => 'From',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'bcc',
            'label'      => 'BCC',
            'attributes' => array(
                'data-help' => 'Send BCC to sender for every entry.',
            ),
        ));

        parent::init();
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'     => 'text',
            'name'     => 'subject',
            'label'    => 'Subject',
            'required' => $isDefault,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'       => 'textarea',
            'name'       => 'body',
            'label'      => 'Body',
            'required'   => $isDefault,
            'attributes' => array(
                'row' => 20,
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }
}
