<?php

namespace CudiBundle\Form\Admin\Mail;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Send extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $email = '';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'hidden',
                'name'    => 'email',
                'value'   => $this->email,
                'options' => array(
                    'input' => array(
                        'required' => true,
                        'filters'  => array(
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
                'type'    => 'hidden',
                'name'    => 'name',
                'value'   => $this->name,
                'options' => array(
                    'input' => array(
                        'required' => true,
                        'filters'  => array(
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
                    'style' => 'width: 350px;',
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
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param  string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
