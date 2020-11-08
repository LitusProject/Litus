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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Event\Subscription;

use BrBundle\Entity\Company;
use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Subscription;

/**
 * Add a corporate relations event.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\Subscription';

    /**
     * @var Event|null
     */
    protected $event;

    /**
     * @var Subscription |null
     */
    protected $subscription;


    const POSSIBLE_UNIVERSITIES = array(
        'ku leuven'     => 'KU Leuven',
        'vub'           => 'Vrije Universiteit Brussel',
        'ugent'         => 'UGent',
        'other'         => 'Other',
    );


    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'first_name',
                'label'    => 'First Name',
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
        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'last_name',
                'label'    => 'Last Name',
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

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'email',
                'label'    => 'Email',
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
                'type'       => 'text',
                'name'       => 'phone_number',
                'label'      => 'Phone Number',
                'attributes' => array(
                    'placeholder' => '+CCAAANNNNNN',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'PhoneNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'university',
                'label'      => 'University',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'university',
                    'options' => ,
                ),
                'options' => array(
                    'input' => array(
                        'required' => count($this->getUniversities()) > 1,
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
                'name'       => 'other_university',
                'label'      => 'University',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'other_university',
                    'hidden'  => true,
                ),
                'options' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'study',
                'label'      => 'Study',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'study',
                    'options' => $this->getStudies(),
                ),
                'options' => array(
                    'input' => array(
                        'required' => count($this->getStudies()) > 1,
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
                'name'       => 'other_study',
                'label'      => 'Study',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'other_study',
                    'hidden'  => true,
                ),
                'options' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'specialization',
                'label'    => 'Specialization',
                'options'  => array(
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
                'type'       => 'select',
                'name'       => 'food',
                'label'      => 'Food',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'food',
                    'options' => $this->getFood(),
                ),
                'options' => array(
                    'input' => array(
                        'required' => count($this->getFood()) > 1,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );


        $this->addSubmit('Add', 'add');

        if ($this->event !== null) {
            $this->bind($this->event);
        }

        if ($this->subscription !== null) {
            $this->bind($this->subscription);
        }
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param  Subscription $subscription
     * @return self
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }


    /**
     * @return array of possible universities
     */
    protected function getStudies(){
        return Event\CompanyMetadata::POSSIBLE_MASTERS;
    }

    /**
     * @return array of possible universities
     */
    protected function getFood(){

    }
}
