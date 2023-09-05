<?php

namespace ShopBundle\Form\Admin\Session\Message;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add message
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{

    protected $hydrator = 'ShopBundle\Hydrator\Session\Message';

    protected function initBeforeTabs()
    {
        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'active',
                'label'    => 'Active',
                'required' => true,
            )
        );
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'textarea',
                'name'     => 'content',
                'label'    => 'Message',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    protected function initAfterTabs()
    {
        $this->addSubmit('Add', 'message_add');
    }
}
