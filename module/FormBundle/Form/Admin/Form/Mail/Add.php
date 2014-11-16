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

namespace FormBundle\Form\Admin\Form\Mail;

/**
 * Add mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'from',
            'label'    => 'From',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
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
                'data-help' => 'Send BCC to sender for every entry',
            ),
        ));

        $this->add(array(
            'type' => 'form_form_mail_content',
            'name' => 'content',
        ));
    }
}
