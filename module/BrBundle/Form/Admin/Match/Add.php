<?php

namespace BrBundle\Form\Admin\Match;

use BrBundle\Entity\StudentCompanyMatch;
use CommonBundle\Component\Form\Admin\Form;

/**
 * Add a student company match
 *
 * @author Robbe Serry <robbe.Serry@vtk.be>
 */
class Add extends Form
{
    protected $hydrator = 'BrBundle\Hydrator\StudentCompanyMatch';

    /**
     * @var StudentCompanyMatch|null
     */
    protected $studentCompanyMatch;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Student',
                'required' => true,
                'options'  => array(
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
                'type'     => 'typeahead',
                'name'     => 'company',
                'label'    => 'Company',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadCompany'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'company_add');

        if ($this->studentCompanyMatch !== null) {
            $this->bind($this->studentCompanyMatch);
        }
    }

    /**
     * @param  StudentCompanyMatch $studentCompanyMatch
     * @return self
     */
    public function setStudentCompanyMatch(StudentCompanyMatch $studentCompanyMatch)
    {
        $this->studentCompanyMatch = $studentCompanyMatch;

        return $this;
    }
}
