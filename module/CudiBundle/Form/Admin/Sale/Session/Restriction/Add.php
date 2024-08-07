<?php

namespace CudiBundle\Form\Admin\Sale\Session\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CudiBundle\Entity\Sale\Session;
use CudiBundle\Entity\Sale\Session\Restriction\Year as YearRestriction;
use RuntimeException;

/**
 * Add Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Session|null
     */
    private $session;

    public function init()
    {
        if ($this->session === null) {
            throw new RuntimeException('Cannot add a restriction to a null sale session');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'Limit the students that can buy articles during this sale session:
                    <ul>
                        <li><b>Name:</b> restrict by name</li>
                        <li><b>Year:</b> restrict study year</li>
                        <li><b>Study:</b> restrict by study</li>
                    </ul>',
                    'id'        => 'restriction_type',
                    'options'   => array(
                        'name'  => 'Name',
                        'year'  => 'Year',
                        'study' => 'Study',
                    ),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'sale_session_restriction_exists',
                                'options' => array(
                                    'session' => $this->session,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'start_value_name',
                'label'      => 'Start Value',
                'required'   => true,
                'attributes' => array(
                    'class' => 'restriction_value restriction_value_name',
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
                'type'       => 'text',
                'name'       => 'end_value_name',
                'label'      => 'End Value',
                'required'   => true,
                'attributes' => array(
                    'class' => 'restriction_value restriction_value_name',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'sale_session_restriction_values',
                                'options' => array(
                                    'start_value' => 'start_value_name',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'start_value_year',
                'label'      => 'Start Value',
                'required'   => true,
                'attributes' => array(
                    'class'   => 'restriction_value restriction_value_year',
                    'options' => YearRestriction::$possibleYears,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'end_value_year',
                'label'      => 'End Value',
                'required'   => true,
                'attributes' => array(
                    'class'   => 'restriction_value restriction_value_year',
                    'options' => YearRestriction::$possibleYears,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'sale_session_restriction_values',
                                'options' => array(
                                    'start_value' => 'start_value_year',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'value_study',
                'label'      => 'Studies',
                'required'   => true,
                'attributes' => array(
                    'class'    => 'restriction_value restriction_value_study',
                    'id'       => 'restriction_value_study',
                    'multiple' => true,
                    'options'  => $this->getStudies(),
                    'style'    => 'max-width: 100%;',
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Session $session
     * @return self
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }

    public function getStudies()
    {
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager());

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByAcademicYear($academicYear);

        $options = array();
        foreach ($studies as $study) {
            $options[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle();
        }

        return $options;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ($this->data['type'] == 'name') {
            unset($specs['start_value_year']);
            unset($specs['end_value_year']);

            unset($specs['value_study']);
        } elseif ($this->data['type'] == 'year') {
            unset($specs['start_value_name']);
            unset($specs['end_value_name']);

            unset($specs['value_study']);
        } elseif ($this->data['type'] == 'study') {
            unset($specs['start_value_name']);
            unset($specs['end_value_name']);

            unset($specs['start_value_year']);
            unset($specs['end_value_year']);
        }

        return $specs;
    }
}
