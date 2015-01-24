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

namespace PromBundle\Form\Registration;

/**
 * add new registration
 *
 * @author Mathijs Cuppens
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
	public function init()
    {
        parent::init();

        $this->add(array(
        	'type'     => 'fieldset',
            'name'     => 'add',
            'label'    => '',
            'elements' => array(
            	array(
		            'type'       => 'text',
		            'name'       => 'first_name',
		            'label'      => 'First Name',
		            'required'   => true,
		            'options'    => array(
		                'input' => array(
		                    'filters' => array(
		                        array('name' => 'StringTrim'),
		                    ),
		                ),
		            ),
		        ),

            	array(
		            'type'       => 'text',
		            'name'       => 'last_name',
		            'label'      => 'Last Name',
		            'required'   => true,
		            'options'    => array(
		                'input' => array(
		                    'filters' => array(
		                        array('name' => 'StringTrim'),
		                    ),
		                ),
		            ),
		        ),

            	array(
		        	'type'     => 'text',
		            'name'     => 'email',
		            'label'    => 'Email',
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
		        ),

        		array(
		            'type'       => 'text',
		            'name'       => 'ticket_code',
		            'label'      => 'Ticket Code',
		            'required'   => true,
		            'options'    => array(
		                'input' => array(
		                    'filters' => array(
		                        array('name' => 'StringTrim'),
		                    ),
		                ),
		            ),
		        ),

		        array(
		            'type'       => 'select',
		            'name'       => 'first_bus',
		            'label'      => 'Go Bus',
		            'required'   => true,
                    'attributes' => array(
                        'id'      => 'first_bus',
                        'options' => $this->getFirstBusses(),
                    ),
                ),

                array(
		            'type'       => 'select',
		            'name'       => 'second_bus',
		            'label'      => 'Return Bus',
		            'required'   => true,
                    'attributes' => array(
                        'id'      => 'second_bus',
                        'options' => $this->getSecondBusses(),
                    ),
                ),
		    ),
        ));

        $this->addSubmit('Add', 'btn btn-default');
    }

    private function getFirstBusses()
    {
    	$busses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->getGoBusses();

    	$array = array('0' => '');

    	return $array;
    }

	private function getSecondBusses()
    {

    	$array = array('0' => '');

    	return $array;
    }
}