<?php

namespace SyllabusBundle\Form\Admin\Subject\ModuleGroup;

use CommonBundle\Entity\General\AcademicYear;
use RuntimeException;
use SyllabusBundle\Entity\Study\SubjectMap;
use SyllabusBundle\Entity\Subject;

/**
 * Add ModuleGroup to Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Study\SubjectMap';

    /**
     * @var SubjectMap|null
     */
    protected $mapping = null;

    /**
     * @var Subject
     */
    private $subject;

    /**
     * @var AcademicYear
     */
    private $academicYear;

    public function init()
    {
        if ($this->subject === null) {
            throw new RuntimeException('No subject was given to add a module group to');
        }

        if ($this->academicYear === null) {
            throw new RuntimeException('No academic year was given');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'module_group',
                'label'      => 'Module Group',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'module_group',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'TypeaheadStudyModuleGroup',
                            ),
                            array(
                                'name'    => 'SubjectModuleGroup',
                                'options' => array(
                                    'subject'       => $this->subject,
                                    'academic_year' => $this->academicYear,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'mandatory',
                'label' => 'Mandatory',
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Subject $subject
     * @return self
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param  SubjectMap $map
     * @return self
     */
    public function setMapping(SubjectMap $map)
    {
        $this->mapping = $map;

        $this->setSubject($map->getSubject())
            ->setAcademicYear($map->getAcademicYear());

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
