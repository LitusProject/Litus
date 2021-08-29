<?php

namespace FormBundle\Form\Admin\Form\Mail;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    public function init()
    {
        $this->add(
            array(
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
                            array('name' => 'EmailAddress'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'bcc',
                'label'      => 'BCC',
                'attributes' => array(
                    'data-help' => 'Send BCC to sender for every entry.',
                ),
            )
        );

        parent::init();
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
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
            )
        );

        $container->add(
            array(
                'type'       => 'textarea',
                'name'       => 'body',
                'label'      => 'Body',
                'required'   => $isDefault,
                'attributes' => array(
                    'row' => 20,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }
}
