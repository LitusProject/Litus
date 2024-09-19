<?php

namespace CudiBundle\Form\Admin\Retail;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Retail;

/**
 * Add Retail
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Retail';

    /**
     * @var Academic|null
     */
    protected $academic;

    /**
     * @var Retail|null
     */
    protected $retail;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'owner',
                'label'      => 'Owner',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'person',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'article',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadRetail'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'price',
                'label'      => 'Price',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 75px;',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                            array('name' => 'MaximalRetailPrice'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'anonymous',
                'label'      => 'Anonymous',
                'attributes' => array(
                    'data-help' => 'If this flag is enabled, the owner\'s name will not be visible to the buyers.',
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'comment',
                'label'   => 'Comment',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'retail_add');
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @param  Retail $retail
     * @return self
     */
    public function setRetail(Retail $retail)
    {
        $this->retail = $retail;

        return $this;
    }
}
