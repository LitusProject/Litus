<?php

namespace CommonBundle\Form\Admin\Config;

use CommonBundle\Entity\General\Config;
use LogicException;

/**
 * Edit Configuration
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CommonBundle\Hydrator\General\Config';

    /**
     * @var Config|null The config to edit.
     */
    private $config;

    public function init()
    {
        if ($this->config === null) {
            throw new LogicException('Cannot edit a null config');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'key',
                'label'      => 'Key',
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => strlen($this->config->getValue()) > 40 ? 'textarea' : 'text',
                'name'       => 'value',
                'label'      => 'Value',
                'required'   => true,
                'attributes' => array(
                    'id' => 'config_value',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                            array('name' => 'StripCarriageReturn'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'config_edit');

        $this->bind($this->config);
    }

    /**
     * @param  Config $config The config to edit
     * @return self
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }
}
