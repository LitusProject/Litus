<?php

namespace SyllabusBundle\Form\Admin\Poc;

use SyllabusBundle\Entity\Poc;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EditEmail extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Poc';

    /**
     * @var \SyllabusBundle\Entity\Poc|null
     */
    protected $poc = null;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'emailAdress',
                'label'      => 'POC Email Address',
                'required'   => true,
                'attributes' => array(
                    'size' => 70,
                ),
            )
        );

        $this->addSubmit('Save', 'edit');
    }

    public function setPoc(Poc $poc)
    {
        $this->poc = $poc;

        return $this;
    }
}
