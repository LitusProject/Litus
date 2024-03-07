<?php

namespace BrBundle\Form\Admin\Match;

use BrBundle\Entity\StudentCompanyMatch;

/**
 * Add a student company match
 *
 * @author Robbe Serry <robbe.Serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\StudentCompanyMatch';

    /**
     * @var StudentCompanyMatch|null
     */
    protected $studentCompanyMatch;

    public function init()
    {
        parent::init();

        // TODO add student typeahead
        // TODO add company typeahead

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
