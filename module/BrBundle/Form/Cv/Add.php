<?php

namespace BrBundle\Form\Cv;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;

/**
 * Add Cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Cv\Entry';

    /**
     * @var Academic
     */
    protected $academic;

    /**
     * @var AcademicYear
     */
    protected $academicYear;

    /**
     * @var array The possible mobility answers.
     */
    public static $mobilityAnswers = array(
        'Yes please'    => 'Yes please',
        'If necessary'  => 'If necessary',
        'Monthly trips' => 'Monthly trips',
        'Annual trips'  => 'Annual trips',
        'No'            => 'No',
    );

    public function init()
    {
        parent::init();

        list($currentYear, $allYears) = $this->getYears();
        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'personal',
                'label'    => 'Personal',
                'elements' => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'email',
                        'label'    => 'Personal E-mail',
                        'required' => false,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length' => 100,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'studies',
                'label'    => 'Education',
                'elements' => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'prior_degree',
                        'label'      => 'Prior Degree (e.g. Bachelor in Engineering, Industrial Engineering, ...)',
                        'required'   => true,
                        'attributes' => array(
                            'rows'       => 2,
                            'class'      => 'count',
                            'data-count' => 100,
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 100,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'prior_grade',
                        'label'      => 'Grade for the Prior Degree (e.g. 65.48)',
                        'required'   => false,
                        'empty-data' => '0',
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'Decimal',
                                        'options' => array(
                                            'max_after_decimal' => '2',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'bachelor_start',
                        'label'      => 'Started Prior Degree In',
                        'required'   => true,
                        'value'      => $currentYear - 4,
                        'attributes' => array(
                            'options' => $allYears,
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'bachelor_end',
                        'label'      => 'Ended Prior Degree In',
                        'required'   => true,
                        'value'      => $currentYear - 1,
                        'attributes' => array(
                            'options' => $allYears,
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'degree',
                        'label'      => 'Primary Degree',
                        'required'   => true,
                        'attributes' => array(
                            'options' => $this->getStudyMap(),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'grade',
                        'label'      => '(Provisional) Grade for the Current Degree (e.g. 65.48)',
                        'required'   => false,
                        'empty-data' => '0',
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'Decimal',
                                        'options' => array(
                                            'max_after_decimal' => '2',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'master_start',
                        'label'      => 'Started Master In',
                        'required'   => true,
                        'value'      => $currentYear - 1,
                        'attributes' => array(
                            'options' => $allYears,
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'master_end',
                        'label'      => 'Will End Master In',
                        'required'   => true,
                        'value'      => $currentYear + 1,
                        'attributes' => array(
                            'options' => $allYears,
                        ),
                    ),
                    array(
                        'type'       => 'textarea',
                        'name'       => 'additional_diplomas',
                        'label'      => 'Additional Diplomas (e.g. driver\'s license)',
                        'attributes' => array(
                            'rows'       => 2,
                            'class'      => 'count',
                            'data-count' => 100,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 100,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'erasmus',
                'label'    => 'Erasmus (Optional)',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'period',
                        'label'      => 'Period',
                        'attributes' => array(
                            'class'      => 'count',
                            'data-count' => 50,
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 50,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'location',
                        'label'      => 'Location',
                        'attributes' => array(
                            'class'      => 'count',
                            'data-count' => 50,
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 50,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'collection',
                'name'     => 'languages',
                'label'    => 'Languages (max. 4)',
                'options'  => array(
                    'count'                  => 0,
                    'should_create_template' => true,
                    'allow_add'              => true,
                    'target_element'         => array(
                        'type' => 'br_cv_language',
                    ),
                ),
                'elements' => array(

                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'languages_extra',
                'label'    => 'Languages Extra Info',
                'elements' => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'extra',
                        'label'      => 'Extra Information (Year Abroad, Born Outside Belgium, ...)',
                        'attributes' => array(
                            'rows'       => 2,
                            'class'      => 'count',
                            'data-count' => 100,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 100,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'capabilities',
                'label'    => 'Capabilities',
                'elements' => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'computer_skills',
                        'label'      => 'Computer Skills',
                        'required'   => true,
                        'attributes' => array(
                            'rows'       => 4,
                            'class'      => 'count',
                            'data-count' => 250,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 250,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'collection',
                        'name'    => 'experiences',
                        'label'   => 'Experiences, Projects (e.g. Internship, Holiday Jobs) (Max 4)',
                        'options' => array(
                            'count'                  => 0,
                            'should_create_template' => true,
                            'allow_add'              => true,
                            'target_element'         => array(
                                'type' => 'br_cv_experience',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'thesis',
                'label'    => 'Thesis',
                'elements' => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'summary',
                        'label'      => 'Summary',
                        'required'   => true,
                        'attributes' => array(
                            'rows'       => 5,
                            'class'      => 'count',
                            'data-count' => 250,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 250,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'future',
                'label'    => 'Future',
                'elements' => array(
                    array(
                        'type'       => 'select',
                        'name'       => 'mobility_europe',
                        'label'      => 'Mobility Europe (Would you be able to travel within Europe? How often?)',
                        'required'   => true,
                        'value'      => 'No',
                        'attributes' => array(
                            'options' => self::$mobilityAnswers,
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'mobility_world',
                        'label'      => 'Mobility World (Would you be able to travel around the world? How often?)',
                        'required'   => true,
                        'value'      => 'No',
                        'attributes' => array(
                            'options' => self::$mobilityAnswers,
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'profile',
                'label'    => 'Profile',
                'elements' => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'hobbies',
                        'label'      => 'Hobbies',
                        'required'   => true,
                        'attributes' => array(
                            'rows'       => 3,
                            'class'      => 'count',
                            'data-count' => 100,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 100,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'textarea',
                        'name'       => 'about',
                        'label'      => 'About Me',
                        'required'   => true,
                        'attributes' => array(
                            'rows'       => 2,
                            'class'      => 'count',
                            'data-count' => 300,
                            'style'      => 'resize: none;',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'FieldLength',
                                        'options' => array(
                                            'max_length'      => 300,
                                            'new_line_length' => 75,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add');

        $this->remove('csrf');
    }

    private function getStudyMap()
    {
        $studyMap = array();
        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($this->academic, $this->academicYear);

        foreach ($studies as $study) {
            $studyMap[$study->getStudy()->getId()] = $study->getStudy()->getTitle();
        }

        return $studyMap;
    }

    private function getYears()
    {
        $currentYear = date('Y');
        $years = array();
        for ($i = -1; $i < 20; $i++) {
            $year = $currentYear - $i;
            $years[$year] = $year;
        }

        return array($currentYear, $years);
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
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }
}
