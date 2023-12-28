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

namespace BrBundle\Form\Career\Event\Subscription;

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Subscription;
use Laminas\Validator\Identical;

/**
 * Add a corporate relations event.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
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

    public function init()
    {
        parent::init();
        //TODO: language

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

//        $this->add(
//            array(
//                'type'       => 'text',
//                'name'       => 'phone_number',
//                'label'      => 'Phone Number',
//                'attributes' => array(
//                    'placeholder' => '+CCAAANNNNNN',
//                ),
//                'options'    => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                        'validators' => array(
//                            array('name' => 'PhoneNumber'),
//                        ),
//                    ),
//                ),
//            )
//        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'university',
                'label'      => 'University',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'university',
                    'options' => $this->getUniversities(),
                ),
                'options'    => array(
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
                'label'      => 'Other university (if applicable)',
                'required'   => false,
                'attributes' => array(
                    'id'       => 'other_university',
                    'disabled' => true,
                ),
                'options'    => array(
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
                'name'       => 'study',
                'label'      => 'Study (for bachelors, choose the study you would like to do)',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'study',
                    'options' => $this->getStudies(),
                ),
                'options'    => array(
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
                'label'      => 'Other study (if applicable)',
                'required'   => false,
                'attributes' => array(
                    'id'       => 'other_study',
                    'disabled' => true,
                ),
                'options'    => array(
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
                'type'    => 'text',
                'name'    => 'specialization',
                'label'   => 'Specialization (optional)',
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
                'type'       => 'select',
                'name'       => 'study_year',
                'label'      => 'Year of study',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'study_year',
                    'options' => $this->getStudyYears(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        if (count($this->getFood()) > 1) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'food',
                    'label'      => 'Food during event',
                    'required'   => false,
                    'attributes' => array(
                        'id'      => 'food',
                        'options' => $this->getFood(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => count($this->getFood()) > 1,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );
        }

//        $this->add(
//            array(
//                'type'     => 'checkbox',
//                'name'     => 'network_reception',
//                'label'    => 'I will participate in the network reception.',
//                'required' => true,
//            )
//        );
        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'consent',
                'label'      => 'I consent that my personal information can be used within this event for necessary purposes.',
                'attributes' => array(
                    'id' => 'conditions',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'identical',
                                'options' => array(
                                    'token'    => true,
                                    'strict'   => false,
                                    'messages' => array(
                                        Identical::NOT_SAME => 'You must give your consent to allow us to use your personal information.',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Register', 'add');

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
    protected function getStudies()
    {
        return Subscription::POSSIBLE_STUDIES;
    }

    /**
     * @return array of possible universities
     */
    protected function getUniversities()
    {
        return Subscription::POSSIBLE_UNIVERSITIES;
    }

    /**
     * @return array of possible Foods
     */
    protected function getFood()
    {
        $food = $this->event->getFood();
        if ($food != null) {
            $food = array(' ' => ' ') + $food;
        }
        return $food;
    }

    /**
     * @return array of possible study years
     */
    protected function getStudyYears()
    {
        return Subscription::POSSIBLE_STUDY_YEARS;
    }
}
