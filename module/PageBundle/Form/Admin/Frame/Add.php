<?php

namespace PageBundle\Form\Admin\Frame;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use PageBundle\Entity\Frame as FrameEntity;
use RuntimeException;

/**
 * Add Frame
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'PageBundle\Hydrator\Frame';

    /**
     * @var FrameEntity
     */
    private $frame;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'active',
                'label' => 'Active',
                'value' => true,
            )
        );

        $this->addSubmit('Add', 'page_add');

        if ($this->getFrame() !== null) {
            $this->bind($this->getFrame());
        }
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type' => 'textarea',
                'name' => 'description',
                'label' => 'Description',
                'required' => $isDefault,
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
     * @param FrameEntity
     * @return self
     */
    public function setFrame(FrameEntity $frame)
    {
        $this->frame = $frame;

        return $this;
    }

    /**
     * @return FrameEntity
     */
    public function getFrame()
    {
        return $this->frame;
    }
}
