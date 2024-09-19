<?php

namespace PromBundle\Form\Admin\ReservationCode;

/**
 * Add Academic
 *
 * @author Matthias Swiggers <matthias.swiggers@studentit.be>
 */
class Academic extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PromBundle\Hydrator\Bus\ReservationCode\Academic';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'person',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => array(
                    'autofocus' => 'true',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'EntryAcademic',
                            ),
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'number_tickets',
                'label'      => 'Number of tickets',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getNumberOptions(),
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'academic_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'code_add',
                ),
            )
        );
    }

    private function getNumberOptions()
    {
        $options = array();
        for ($i = 0; $i <= 10; $i++) {
            $options[$i] = $i;
        }

        return $options;
    }
}
