<?php

namespace MailBundle\Form\Admin\Group;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $from = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('system_mail_address');

        $name = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('system_mail_name');

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'from',
                'label'      => 'From',
                'required'   => true,
                'value'      => $from,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
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
                'type'       => 'text',
                'name'       => 'name',
                'label'      => 'Sender Name',
                'required'   => true,
                'value'      => $name,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'message',
                'label'      => 'Message',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 500px; height: 200px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'test',
                'label' => 'Test Mail',
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'send',
                'value'      => 'Send',
                'attributes' => array(
                    'class' => 'mail',
                ),
            )
        );
    }
}
